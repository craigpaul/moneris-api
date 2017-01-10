<?php

namespace CraigPaul\Moneris;

trait Preparable
{
    /**
     * Prepare the receipt data.
     *
     * @param $data
     *
     * @return array
     */
    protected function prepare($data, array $params)
    {
        $array = [];

        foreach ($params as $param) {
            $key = $param['key'];
            $property = $param['property'];

            if ($key === 'ResolveData' && count($data->xpath('//ResolveData')) > 1) {
                $resolves = $data->xpath('//ResolveData');

                foreach ($resolves as $index => $resolve) {
                    $resolves[$index] = array_map('strval', (array)$resolve);
                }

                $array[$property] = $resolves;
            } else {
                if (is_array($data)) {
                    $array[$property] = isset($data[$key]) && !is_null($data[$key]) ? $data[$key] : null;
                } else {
                    $array[$property] = isset($data->$key) && !is_null($data->$key) ? $data->$key : null;
                }

                if (isset($param['cast'])) {
                    switch ($param['cast']) {
                        case 'boolean':
                            $array[$property] = isset($array[$property]) ? (is_string($array[$property]) ? $array[$property] : $array[$property]->__toString()) : null;
                            $array[$property] = isset($array[$property]) && !is_null($array[$property]) ? ($array[$property] === 'true' ? true : false) : false;

                            break;
                        case 'float':
                            $array[$property] = isset($array[$property]) ? (is_string($array[$property]) ? floatval($array[$property]) : floatval($array[$property]->__toString())) : null;

                            break;
                        case 'string':
                            $array[$property] = isset($array[$property]) ? (is_string($array[$property]) ? $array[$property] : $array[$property]->__toString()) : null;

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
        }

        return $array;
    }
}
