<?php

use GuzzleHttp\Client;
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
     * The Processor instance.
     *
     * @var \CraigPaul\Moneris\Processor
     */
    protected $processor;

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
        $this->gateway = Moneris::create($this->id, $this->token, $this->params);
        $this->params = [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $this->transaction = new Transaction($this->gateway, $this->params);
        $this->processor = new Processor(new Client());
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $processor = new Processor(new Client());

        $this->assertEquals(Processor::class, get_class($processor));
    }

    /** @test */
    public function it_responds_to_an_invalid_transaction_with_the_proper_code_and_status()
    {
        $transaction = new Transaction($this->gateway);

        $response = $this->processor->process($transaction);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_TRANSACTION_DATA, $response->status);
    }

    /** @test */
    public function it_can_submit_a_proper_request_to_the_moneris_api()
    {
        $response = $this->processor->process($this->transaction);

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_submit_a_avs_secured_request_to_the_moneris_api()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'avs' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
        ]);

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_submit_a_cvd_secured_request_to_the_moneris_api()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'cvd' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'cvd' => '111'
        ]);

        $this->assertTrue($response->successful);
    }
}