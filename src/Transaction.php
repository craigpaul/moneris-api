<?php

namespace CraigPaul\Moneris;

use SimpleXMLElement;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property-read array $errors
 * @property-read \CraigPaul\Moneris\Gateway $gateway
 * @property-read array $params
 * @property SimpleXMLElement $response
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
     * @var SimpleXMLElement
     */
    protected $response;

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
     * Check that the required parameters have not been provided to the transaction.
     *
     * @return bool
     */
    public function invalid()
    {
        return !$this->valid();
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
        unset($params['type']);

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
                case 'purchase':
                    $errors[] = Validator::set($params, 'order_id') ? null : 'Order Id not provided.';
                    $errors[] = Validator::set($params, 'pan') ? null : 'Credit card number not provided.';
                    $errors[] = Validator::set($params, 'amount') ? null : 'Amount not provided.';
                    $errors[] = Validator::set($params, 'expdate') ? null : 'Expiry date not provided.';

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