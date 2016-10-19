<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property-read string $id
 * @property-read string $token
 * @property-read string $environment
 */
class Gateway
{
    use Gettable;

    /**
     * The environment used for connecting to the Moneris API.
     *
     * @var string
     */
    protected $environment;

    /**
     * The Moneris Store ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Moneris API Token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new Moneris instance.
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