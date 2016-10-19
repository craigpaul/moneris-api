<?php

use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Moneris;

class GatewayTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Moneris gateway.
     *
     * @var \CraigPaul\Moneris\Gateway
     */
    protected $gateway;

    /**
     * The environment used for connecting to the Moneris API.
     *
     * @var string
     */
    protected $environment;

    /**
     * The Moneris store id.
     *
     * @var string
     */
    protected $id;

    /**
     * The Moneris API parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * The Moneris API token.
     *
     * @var string
     */
    protected $token;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->id = 'store1';
        $this->token = 'yesguy';
        $this->environment = Moneris::ENV_TESTING;
        $this->params = [
            'environment' => $this->environment
        ];

        $this->gateway = Moneris::create($this->id, $this->token, $this->params)->gateway();
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $gateway = new Gateway($this->id, $this->token, $this->environment);

        $this->assertEquals(Gateway::class, get_class($gateway));
        $this->assertObjectHasAttribute('id', $gateway);
        $this->assertObjectHasAttribute('token', $gateway);
        $this->assertObjectHasAttribute('environment', $gateway);
    }

    /** @test */
    public function it_can_access_properties_of_the_class()
    {
        $this->assertEquals($this->id, $this->gateway->id);
        $this->assertEquals($this->token, $this->gateway->token);
        $this->assertEquals($this->environment, $this->gateway->environment);
    }
}