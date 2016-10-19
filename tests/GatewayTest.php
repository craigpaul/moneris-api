<?php

use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Exceptions\ValidationException;

class GatewayTest extends TestCase
{
    /**
     * The Moneris gateway.
     *
     * @var \CraigPaul\Moneris\Gateway
     */
    protected $gateway;

    /**
     * The Moneris API parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->params = ['environment' => $this->environment];
        $this->gateway = Moneris::create($this->id, $this->token, $this->params)->connect();
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

    /** @test */
    public function it_can_make_a_purchase_and_receive_a_response()
    {
        $params = [
            'order_id' => '1234-567890',
            'amount' => '1.00',
            'pan' => $this->visa,
            'expdate' => '2012',
        ];

        $response = $this->gateway->purchase($params);

        $this->assertEquals(Response::class, get_class($response));
    }
}