<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property-read \CraigPaul\Moneris\Gateway $gateway
 * @property-read array $params
 */
class Transaction
{
    use Gettable;

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
     * Create a new Transaction instance.
     *
     * @param \CraigPaul\Moneris\Gateway $gateway
     * @param array $params
     * @param bool $prepare
     */
    public function __construct(Gateway $gateway, array $params = [], $prepare = true)
    {
        $this->gateway = $gateway;
        $this->params = $prepare ? $this->prepare($params) : $params;
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

        if (isset($params['pan'])) {
            $params['pan'] = preg_replace('/\D/', '', $params['pan']);
        }

        return $params;
    }
}