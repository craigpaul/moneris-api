<?php

use CraigPaul\Moneris\Crypt;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Transaction;

class ResponseTest extends TestCase
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
     * The Response instance.
     *
     * @var \CraigPaul\Moneris\Response
     */
    protected $response;

    /**
     * The Transaction instance.
     *
     * @var \CraigPaul\Moneris\Transaction
     */
    protected $transaction;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->params = ['environment' => Moneris::ENV_TESTING];
        $this->gateway = Moneris::create($this->id, $this->token, $this->params)->connect();
        $this->params = [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $this->transaction = new Transaction($this->gateway, $this->params);
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $response = new Response($this->transaction);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertObjectHasAttribute('status', $response);
        $this->assertObjectHasAttribute('successful', $response);
        $this->assertObjectHasAttribute('transaction', $response);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $response = Response::create($this->transaction);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertObjectHasAttribute('status', $response);
        $this->assertObjectHasAttribute('successful', $response);
        $this->assertObjectHasAttribute('transaction', $response);
    }

    /** @test */
    public function it_can_validate_an_api_response_from_a_proper_transaction()
    {
        $response = Processor::process($this->transaction);

        $response = $response->validate();

        $this->assertTrue($response->successful);
    }
}