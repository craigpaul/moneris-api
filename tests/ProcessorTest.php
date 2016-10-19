<?php

use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\Response;
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
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->params = ['environment' => Moneris::ENV_TESTING];
        $this->gateway = Moneris::create($this->id, $this->token, $this->params)->connect();
    }

    /** @test */
    public function it_responds_to_an_invalid_transaction_with_the_proper_code_and_status()
    {
        $transaction = new Transaction($this->gateway);

        $response = Processor::process($transaction);

        $this->assertFalse($response->successful);
        $this->assertEquals(Response::INVALID_TRANSACTION_DATA, $response->status);
    }
}