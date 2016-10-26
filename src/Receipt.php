<?php

namespace CraigPaul\Moneris;

class Receipt
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Create a new Receipt instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $this->prepare($data);
    }

    /**
     * Prepare the receipt data.
     *
     * @param $data
     *
     * @return array
     */
    protected function prepare($data)
    {
        $array = [];
        $params = [
            ['property' => 'amount', 'key' => 'TransAmount', 'cast' => 'float'],
            ['property' => 'authorization', 'key' => 'AuthCode', 'cast' => 'string'],
            ['property' => 'avs_result', 'key' => 'AvsResultCode', 'cast' => 'string'],
            ['property' => 'card', 'key' => 'CardType', 'cast' => 'string'],
            ['property' => 'code', 'key' => 'ResponseCode', 'cast' => 'string'],
            ['property' => 'complete', 'key' => 'Complete', 'cast' => 'boolean'],
            ['property' => 'cvd_result', 'key' => 'CvdResultCode', 'cast' => 'string'],
            ['property' => 'data', 'key' => 'ResolveData', 'cast' => 'array', 'callback' => 'setData'],
            ['property' => 'date', 'key' => 'TransDate', 'cast' => 'string'],
            ['property' => 'id', 'key' => 'ReceiptId', 'cast' => 'string'],
            ['property' => 'iso', 'key' => 'ISO', 'cast' => 'string'],
            ['property' => 'key', 'key' => 'DataKey', 'cast' => 'string'],
            ['property' => 'message', 'key' => 'Message', 'cast' => 'string'],
            ['property' => 'reference', 'key' => 'ReferenceNum', 'cast' => 'string'],
            ['property' => 'time', 'key' => 'TransTime', 'cast' => 'string'],
            ['property' => 'transaction', 'key' => 'TransID', 'cast' => 'string'],
            ['property' => 'type', 'key' => 'TransType', 'cast' => 'string'],
        ];

        foreach ($params as $param) {
            $key = $param['key'];
            $property = $param['property'];

            $array[$property] = isset($data->$key) && !is_null($data->$key) ? $data->$key : null;

            if (isset($param['cast'])) {
                switch ($param['cast']) {
                    case 'boolean':
                        $array[$property] = isset($array[$property]) ? $array[$property]->__toString() : null;
                        $array[$property] = isset($array[$property]) && !is_null($array[$property]) ? ($array[$property] === 'true' ? true : false) : false;

                        break;
                    case 'float':
                        $array[$property] = isset($array[$property]) ? floatval($array[$property]->__toString()) : null;

                        break;
                    case 'string':
                        $array[$property] = isset($array[$property]) ? $array[$property]->__toString() : null;

                        break;
                    case 'array':
                        $array[$property] = (array)$array[$property];
                }
            }

            if (isset($param['callback'])) {
                $callback = $param['callback'];

                $array[$property] = $this->$callback($array[$property]);
            }
        }

        return $array;
    }

    /**
     * Read an item from the receipt.
     *
     * @param string $value
     *
     * @return mixed|null
     */
    public function read($value = '')
    {
        if (isset($this->data[$value]) && !is_null($this->data[$value])) {
            return $this->data[$value];
        }

        return null;
    }

    /**
     * Format the resolved data from the Moneris API.
     *
     * @param array $data
     *
     * @return array
     */
    private function setData(array $data)
    {
        return [
            'customer_id' => isset($data['cust_id']) ? (is_string($data['cust_id']) ? $data['cust_id'] : $data['cust_id']->__toString()) : null,
            'phone' => isset($data['phone']) ? (is_string($data['phone']) ? $data['phone'] : $data['phone']->__toString()) : null,
            'email' => isset($data['email']) ? (is_string($data['email']) ? $data['email'] : $data['email']->__toString()) : null,
            'note' => isset($data['note']) ? (is_string($data['note']) ? $data['note'] : $data['note']->__toString()) : null,
            'crypt' => isset($data['crypt_type']) ? intval($data['crypt_type']) : null,
            'masked_pan' => isset($data['masked_pan']) ? $data['masked_pan'] : null,
            'pan' => isset($data['pan']) ? $data['pan'] : null,
            'expiry_date' => [
                'month' => isset($data['expdate']) ? substr($data['expdate'], -2, 2) : null,
                'year' => isset($data['expdate']) ? substr($data['expdate'], 0, 2) : null,
            ],
        ];
    }
}
