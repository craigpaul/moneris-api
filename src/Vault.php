<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Vault
 *
 * @property-read string $environment
 * @property-read string $id
 * @property-read string $token
 */
class Vault extends Gateway
{
    use Gettable;

    /**
     * Create a new Vault instance.
     *
     * @param string $id
     * @param string $token
     * @param string $environment
     *
     * @return void
     */
    public function __construct(string $id, string $token, string $environment)
    {
        parent::__construct($id, $token, $environment);
    }

    /**
     * @param \CraigPaul\Moneris\CreditCard $card
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function add(CreditCard $card)
    {
        $params = [
            'type' => 'res_add_cc',
            'crypt_type' => $card->crypt,
            'pan' => $card->number,
            'expdate' => $card->expiry,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Create a new Vault instance.
     *
     * @param string $id
     * @param string $token
     * @param string $environment
     *
     * @return $this
     */
    public static function create(string $id, string $token, string $environment)
    {
        return new static($id, $token, $environment);
    }
}