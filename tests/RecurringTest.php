<?php

use Faker\Factory as Faker;
use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Response;
use CraigPaul\Moneris\Receipt;
use Carbon\Carbon;

class RecurringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $token;

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

        $this->id = 'store2';
        $this->token = 'yesguy';
        $this->environment = Moneris::ENV_TESTING;

        $faker = Faker::create();
        $params = ['environment' => $this->environment];
        $this->gateway = Moneris::create($this->id, $this->token, $params);
        $this->params = [
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => '4242424242424242',
            'expdate' => '2012',
            'recur' => [
                'recur_unit' => 'day',
                'num_recurs' => 1,
                'start_now' => 'false',
                'period' => 1,
                'recur_amount' => '1.00'
            ]
        ];
    }

    /** @test */
    public function it_can_make_a_recurring_purchase () {
        $this->params['recur']['start_date'] = Carbon::now()->addDays(2)->format('Y/m/d');
        $response = $this->gateway->purchase($this->params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertEquals(Receipt::class,get_class($receipt = $response->receipt()));
        $this->assertTrue($receipt->successful());
    }

    /** @test */
    public function it_can_fail_with_a_recurring_purchase () {
        // fails on date being the same as today
        $this->params['recur']['start_date'] = Carbon::now()->format('Y/m/d');
        $response = $this->gateway->purchase($this->params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertEquals(Receipt::class,get_class($receipt = $response->receipt()));
        $this->assertFalse($receipt->successful());
    }

}
