<?php

use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Transaction;

class TransactionTest extends TestCase
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
            'order_id' => '1234-567890',
            'amount' => '1.00',
            'pan' => $this->visa,
            'expdate' => '2012',
        ];
        $this->transaction = new Transaction($this->gateway, $this->params);
    }

    /** @test */
    public function it_can_access_properties_of_the_class()
    {
        $this->assertEquals($this->gateway, $this->transaction->gateway);
        $this->assertEquals($this->params, $this->transaction->params);
    }

    /** @test */
    public function it_can_prepare_parameters_that_were_submitted_improperly()
    {
        $order = '   1234-567890';
        $card = '4242 4242 4242 4242';
        $transaction = new Transaction($this->gateway, [
            'order_id' => $order,
            'amount' => '1.00',
            'pan' => $card,
            'expdate' => '2012',
        ]);

        $this->assertEquals(trim($order), $transaction->params['order_id']);
        $this->assertEquals(preg_replace('/\D/', '', $card), $transaction->params['pan']);
    }
}