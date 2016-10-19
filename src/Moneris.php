<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Moneris
 *
 * @property-read string $id
 * @property-read string $token
 * @property-read string $environment
 * @property-read string $params
 */
class Moneris
{
    use Gettable;

    const ENV_LIVE    = 'live';
    const ENV_STAGING = 'staging';
    const ENV_TESTING = 'testing';

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
     * The extra parameters needed for Moneris.
     *
     * @var array
     */
    protected $params;

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
    public function __construct(string $id, string $token, array $params = [])
    {
        $this->id = $id;
        $this->token = $token;
        $this->environment = $params['environment'] ?? self::ENV_LIVE;
        $this->params = $params;
    }

    /**
     * Create a new Moneris instance.
     *
     * @param string $id
     * @param string $token
     * @param array $params
     *
     * @return $this
     */
    public static function create(string $id, string $token, array $params = [])
    {
        return new static($id, $token, $params);
    }

    /**
     * Create and return a new Gateway instance.
     *
     * @return \CraigPaul\Moneris\Gateway
     */
    public function connect()
    {
        return new Gateway($this->id, $this->token, $this->environment);
    }
}