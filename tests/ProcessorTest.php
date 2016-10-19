<?php

use CraigPaul\Moneris\Crypt;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Transaction;

class ProcessorTest extends TestCase
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
            'pan' => $this->visa,
            'expdate' => '2012',
        ];
        $this->transaction = new Transaction($this->gateway, $this->params);
    }

    /** @test */
    public function it_responds_to_an_invalid_transaction_with_the_proper_code_and_status()
    {
        $transaction = new Transaction($this->gateway);

        $response = Processor::process($transaction);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_TRANSACTION_DATA, $response->status);
    }

    /** @test */
    public function it_can_submit_a_proper_request_to_the_moneris_api()
    {
        $response = Processor::process($this->transaction);

        $this->assertTrue($response->successful);
    }
}