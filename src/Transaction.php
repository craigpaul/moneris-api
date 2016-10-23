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
            $params['expdate'] = sprintf('%02%02d', $params['expiry_year'], $params['expiry_monthp']);
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
            ['purchase', 'preauth', 'card_verification', 'cavv_purchase', 'cavv_preauth']
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

        foreach ($params as $key => $value) {
            $type->addChild($key, $value);
        }

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

        $errors[] = Validator::empty($params) ? 'No parameters provided.' : null;

        if (isset($params['type'])) {
            switch ($params['type']) {
                case 'res_get_expiring':
                    break;
                case 'card_verification':
                case 'preauth':
                case 'purchase':
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order Id not provided.';
                    $errors[] = Validator::set($params, 'pan') ? null : 'Credit card number not provided.';
                    $errors[] = Validator::set($params, 'amount') ? null : 'Amount not provided.';
                    $errors[] = Validator::set($params, 'expdate') ? null : 'Expiry date not provided.';

                    if ($this->gateway->avs) {
                        $errors[] = Validator::set($params, 'avs_street_number') ? null : 'Street number not provided.';
                        $errors[] = Validator::set($params, 'avs_street_name') ? null : 'Street name not provided.';
                        $errors[] = Validator::set($params, 'avs_zipcode') ? null : 'Postal/Zip code not provided.';
                    }

                    if ($this->gateway->cvd) {
                        $errors[] = Validator::set($params, 'cvd') ? null : 'CVD not provided.';
                    }

                    break;
                case 'res_tokenize_cc':
                case 'purchasecorrection':
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order id not provided.';
                    $errors[] = Validator::set($params, 'txn_number') ? null : 'Transaction number not provided.';

                    break;
                case 'completion':
                    $errors[] = Validator::set($params, 'comp_amount') ? null : 'Amount not provided.';
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order id not provided.';
                    $errors[] = Validator::set($params, 'txn_number') ? null : 'Transaction number not provided.';

                    break;
                case 'refund':
                    $errors[] = Validator::set($params, 'amount') ? null : 'Amount not provided.';
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order id not provided.';
                    $errors[] = Validator::set($params, 'txn_number') ? null : 'Transaction number not provided.';

                    break;
                case 'res_add_cc':
                    $errors[] = Validator::set($params, 'pan') ? null : 'Credit card number not provided.';
                    $errors[] = Validator::set($params, 'expdate') ? null : 'Expiry date not provided.';

                    break;
                case 'res_update_cc':
                    $errors[] = Validator::set($params, 'data_key') ? null : 'Data key not provided.';
                    $errors[] = Validator::set($params, 'pan') ? null : 'Credit card number not provided.';
                    $errors[] = Validator::set($params, 'expdate') ? null : 'Expiry date not provided.';

                    break;
                case 'res_delete':
                case 'res_lookup_full':
                case 'res_lookup_masked':
                    $errors[] = Validator::set($params, 'data_key') ? null : 'Data key not provided.';

                    break;
                case 'res_purchase_cc':
                    $errors[] = Validator::set($params, 'data_key') ? null : 'Data key not provided.';
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order id not provided.';
                    $errors[] = Validator::set($params, 'amount') ? null : 'Amount not provided.';

                    break;
                default:
                    $errors[] = $params['type'].' is not a supported transaction type.';
            }
        } else {
            $errors[] = 'Transaction type not provided.';
        }

        $errors = array_filter($errors);
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