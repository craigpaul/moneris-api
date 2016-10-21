<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Vault
 *
 * @property-read string $environment
 * @property-read string $id
 * @property-read string $token
 */
class Vault
{
    use Gettable;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $environment;

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
        $this->id = $id;
        $this->token = $token;
        $this->environment = $environment;
    }
}