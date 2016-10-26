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
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $this->transaction = new Transaction($this->gateway, $this->params);
    }

    /** @test */
    public function it_can_access_properties_of_the_class()
    {
        $params = $this->params;
        $params['pan'] = $params['credit_card'];
        unset($params['credit_card']);

        $this->assertEquals($this->gateway, $this->transaction->gateway);
        $this->assertEquals($params, $this->transaction->params);
    }

    /** @test */
    public function it_converts_month_year_to_expdate()
    {
        $params = array_merge($this->params, [
            'expiry_month' => '12',
            'expiry_year' => '20'
        ]);

        unset($params['expdate']);

        $transaction = new Transaction($this->gateway, $params);

        $this->assertEquals('2012', $transaction->params['expdate']);
    }

    /** @test */
    public function it_can_prepare_parameters_that_were_submitted_improperly()
    {
        $order = '   1234-567890';
        $card = '4242 4242 4242 4242';
        $transaction = new Transaction($this->gateway, [
            'type' => 'purchase',
            'order_id' => $order,
            'amount' => '1.00',
            'credit_card' => $card,
            'expdate' => '2012',
        ]);

        $this->assertEquals(trim($order), $transaction->params['order_id']);
        $this->assertEquals(preg_replace('/\D/', '', $card), $transaction->params['pan']);
    }

    /** @test */
    public function it_can_determine_that_a_proper_set_of_parameters_has_been_provided_to_the_transaction()
    {
        $this->assertTrue($this->transaction->valid());
        $this->assertFalse($this->transaction->invalid());
    }

    /** @test */
    public function it_can_determine_that_an_improper_set_of_parameters_has_been_provided_to_the_transaction()
    {
        $transaction = new Transaction($this->gateway);

        $this->assertFalse($transaction->valid());
        $this->assertTrue($transaction->invalid());
    }

    /** @test */
    public function it_can_transform_itself_to_an_xml_structure()
    {
        $xml = $this->transaction->toXml();
        $xml = simplexml_load_string($xml);

        $this->assertNotEquals(false, $xml);
    }
}
