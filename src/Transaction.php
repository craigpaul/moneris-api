<?php

namespace CraigPaul\Moneris;

use SimpleXMLElement;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property-read array $errors
 * @property-read \CraigPaul\Moneris\Gateway $gateway
 * @property-read array $params
 * @property \SimpleXMLElement|null $response
 */
class Transaction
{
    use Gettable, Settable;

    const EMPTY_PARAMETERS             = 1;
    const PARAMETER_NOT_SET            = 2;
    const UNSUPPORTED_TRANSACTION_TYPE = 3;

    /**
     * The errors for the transaction.
     *
     * @var array
     */
    protected $errors;

    /**
     * The Gateway instance.
     *
     * @var \CraigPaul\Moneris\Gateway
     */
    protected $gateway;

    /**
     * The extra parameters needed for Moneris.
     *
     * @var array
     */
    protected $params;

    /**
     * @var \SimpleXMLElement|null
     */
    protected $response = null;

    /**
     * Create a new Transaction instance.
     *
     * @param \CraigPaul\Moneris\Gateway $gateway
     * @param array $params
     */
    public function __construct(Gateway $gateway, array $params = [])
    {
        $this->gateway = $gateway;
        $this->params = $this->prepare($params);
    }

    /**
     * Retrieve the amount for the transaction. The is only available on certain transaction types.
     *
     * @return string|null
     */
    public function amount()
    {
        if (isset($this->params['amount'])) {
            return $this->params['amount'];
        }

        return null;
    }

    /**
     * Append elements to the XML response.
     *
     * @param array $params
     * @param \SimpleXMLElement $type
     *
     * @return void
     */
    protected function append(array $params, SimpleXMLElement $type)
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                if ($key === 'items') {
                    foreach ($value as $item) {
                        $parent = $type->addChild('item');
                        $parent->addChild('name', isset($item['name']) ? $item['name'] : '');
                        $parent->addChild('quantity', isset($item['quantity']) ? $item['quantity'] : '');
                        $parent->addChild('product_code', isset($item['product_code']) ? $item['product_code'] : '');
                        $parent->addChild('extended_amount', isset($item['extended_amount']) ? $item['extended_amount'] : '');
                    }
                } else {
                    $parent = $type->addChild($key);

                    $this->append($value, $parent);
                }
            } else {
                $type->addChild($key, $value);
            }
        }
    }

    /**
     * Check that the required parameters have not been provided to the transaction.
     *
     * @return bool
     */
    public function invalid()
    {
        return !$this->valid();
    }

    /**
     * Retrieve the transaction number, assuming the transaction has been processed.
     *
     * @return null|string
     */
    public function number()
    {
        if (is_null($this->response)) {
            return null;
        }

        return (string)$this->response->receipt->TransID;
    }

    /**
     * Retrieve the order id for the transaction. The is only available on certain transaction types.
     *
     * @return string|null
     */
    public function order()
    {
        if (isset($this->params['order_id'])) {
            return $this->params['order_id'];
        }

        return null;
    }

    /**
     * Prepare the transaction parameters.
     *
     * @param array $params
     *
     * @return array
     */
    protected function prepare(array $params)
    {
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $params[$key] = trim($value);
            }

            if ($params[$key] == '') {
                unset($params[$key]);
            }
        }

        if (isset($params['credit_card'])) {
            $params['pan'] = preg_replace('/\D/', '', $params['credit_card']);
            unset($params['credit_card']);
        }

        if (isset($params['description'])) {
            $params['dynamic_descriptor'] = $params['description'];
            unset($params['description']);
        }

        if (isset($params['expiry_month']) && isset($params['expiry_year']) && !isset($params['expdate'])) {
            $params['expdate'] = sprintf('%02d%02d', $params['expiry_year'], $params['expiry_month']);
            unset($params['expiry_year'], $params['expiry_month']);
        }

        return $params;
    }

    /**
     * Convert the transaction parameters into an XML structure.
     *
     * @return string|bool
     */
    public function toXml()
    {
        $gateway = $this->gateway;
        $params = $this->params;

        $type = in_array($params['type'], ['txn', 'acs']) ? 'MpiRequest' : 'request';

        $xml = new SimpleXMLElement("<$type/>");
        $xml->addChild('store_id', $gateway->id);
        $xml->addChild('api_token', $gateway->token);

        $type = $xml->addChild($params['type']);
        $efraud = in_array(
            $params['type'],
            [
                'purchase',
                'preauth',
                'card_verification',
                'cavv_purchase',
                'cavv_preauth',
                'res_purchase_cc',
                'res_preauth_cc'
            ]
        );
        unset($params['type']);

        if ($gateway->cvd && $efraud) {
            $cvd = $type->addChild('cvd_info');
            $cvd->addChild('cvd_indicator', '1');
            $cvd->addChild('cvd_value', $params['cvd']);
            unset($params['cvd']);
        }

        if ($gateway->avs && $efraud) {
            $avs = $type->addChild('avs_info');

            foreach ($params as $key => $value) {
                if (substr($key, 0, 4) !== 'avs_') {
                    continue;
                }

                $avs->addChild($key, $value);
                unset($params[$key]);
            }
        }

        $this->append($params, $type);

        return $xml->asXML();
    }

    /**
     * Check that the required parameters have been provided to the transaction.
     *
     * @return bool
     */
    public function valid()
    {
        $params = $this->params;
        $errors = [];

        $errors[] = empty($params) ? ['field' => 'all', 'code' => self::EMPTY_PARAMETERS, 'title' => 'empty'] : null;

        if (isset($params['type'])) {
            switch ($params['type']) {
                case 'res_get_expiring':
                    break;
                case 'card_verification':
                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['pan']) ? null : [
                        'field' => 'credit_card',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['expdate']) ? null : [
                        'field' => 'expdate',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    if ($this->gateway->avs) {
                        $errors[] = isset($params['avs_street_number']) ? null : [
                            'field' => 'avs_street_number',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_street_name']) ? null : [
                            'field' => 'avs_street_name',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_zipcode']) ? null : [
                            'field' => 'avs_zipcode',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    if ($this->gateway->cvd) {
                        $errors[] = isset($params['cvd']) ? null : [
                            'field' => 'cvd',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    break;
                case 'preauth':
                case 'purchase':
                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['pan']) ? null : [
                        'field' => 'credit_card',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['amount']) ? null : [
                        'field' => 'amount',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['expdate']) ? null : [
                        'field' => 'expdate',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    if ($this->gateway->avs) {
                        $errors[] = isset($params['avs_street_number']) ? null : [
                            'field' => 'avs_street_number',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_street_name']) ? null : [
                            'field' => 'avs_street_name',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_zipcode']) ? null : [
                            'field' => 'avs_zipcode',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    if ($this->gateway->cvd) {
                        $errors[] = isset($params['cvd']) ? null : [
                            'field' => 'cvd',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    break;
                case 'res_tokenize_cc':
                case 'purchasecorrection':
                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['txn_number']) ? null : [
                        'field' => 'txn_number',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'completion':
                    $errors[] = isset($params['comp_amount']) ? null : [
                        'field' => 'comp_amount',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['txn_number']) ? null : [
                        'field' => 'txn_number',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'refund':
                    $errors[] = isset($params['amount']) ? null : [
                        'field' => 'amount',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['txn_number']) ? null : [
                        'field' => 'txn_number',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'res_add_cc':
                    $errors[] = isset($params['pan']) ? null : [
                        'field' => 'pan',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['expdate']) ? null : [
                        'field' => 'expdate',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'res_update_cc':
                    $errors[] = isset($params['data_key']) ? null : [
                        'field' => 'data_key',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['pan']) ? null : [
                        'field' => 'pan',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['expdate']) ? null : [
                        'field' => 'expdate',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'res_delete':
                case 'res_lookup_full':
                case 'res_lookup_masked':
                    $errors[] = isset($params['data_key']) ? null : [
                        'field' => 'data_key',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    break;
                case 'res_preauth_cc':
                case 'res_purchase_cc':
                    $errors[] = isset($params['data_key']) ? null : [
                        'field' => 'data_key',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['order_id']) ? null : [
                        'field' => 'order_id',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    $errors[] = isset($params['amount']) ? null : [
                        'field' => 'amount',
                        'code' => self::PARAMETER_NOT_SET,
                        'title' => 'not_set'
                    ];

                    if ($this->gateway->avs) {
                        $errors[] = isset($params['avs_street_number']) ? null : [
                            'field' => 'avs_street_number',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_street_name']) ? null : [
                            'field' => 'avs_street_name',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];

                        $errors[] = isset($params['avs_zipcode']) ? null : [
                            'field' => 'avs_zipcode',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    if ($this->gateway->cvd) {
                        $errors[] = isset($params['cvd']) ? null : [
                            'field' => 'cvd',
                            'code' => self::PARAMETER_NOT_SET,
                            'title' => 'not_set'
                        ];
                    }

                    break;
                default:
                    $errors[] = [
                        'field' => 'type',
                        'code' => self::UNSUPPORTED_TRANSACTION_TYPE,
                        'title' => 'unsupported_transaction'
                    ];
            }
        } else {
            $errors[] = [
                'field' => 'type',
                'code' => self::PARAMETER_NOT_SET,
                'title' => 'not_set'
            ];
        }

        $errors = array_values(array_filter($errors));
        $this->errors = $errors;

        return empty($errors);
    }

    /**
     * Validate the result of the Moneris API call.
     *
     * @param \SimpleXMLElement $result
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function validate(SimpleXMLElement $result)
    {
        $this->response = $result;

        $response = Response::create($this);
        $response->validate();

        return $response;
    }
}
