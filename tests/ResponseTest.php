<?php

use GuzzleHttp\Client;
use CraigPaul\Moneris\Crypt;
use CraigPaul\Moneris\Receipt;
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
     * The Processor instance.
     *
     * @var \CraigPaul\Moneris\Processor
     */
    protected $processor;

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
        $response = $this->processor->process($this->transaction);

        $response = $response->validate();

        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_receive_a_receipt_from_a_properly_processed_transaction()
    {
        $response = $this->processor->process($this->transaction);

        /** @var \CraigPaul\Moneris\Response $response */
        $response = $response->validate();
        $receipt = $response->receipt();

        $this->assertNotNull($receipt);
        $this->assertEquals(Receipt::class, get_class($receipt));
        $this->assertEquals($this->params['order_id'], $receipt->read('id'));
        $this->assertObjectHasAttribute('data', $receipt);
    }

    /** @test */
    public function it_processes_expdate_error_edge_cases_from_message()
    {
        $response = $this->process_transaction([
            'expdate' => 'foo'
        ]);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_EXPIRY_DATE, $response->status);
    }

    /** @test */
    public function it_processes_credit_card_error_edge_cases_from_message()
    {
        $response = $this->process_transaction([
            'credit_card' => '1234'
        ]);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_CARD, $response->status);
    }

    protected function process_transaction($extra_params = []) {
        $this->params = array_merge($this->params, $extra_params);
        $this->transaction = new Transaction($this->gateway, $this->params);

        $response = $this->processor->process($this->transaction);
        $response = $response->validate();

        return $response;
    }
}
