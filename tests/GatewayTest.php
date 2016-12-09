<?php

use Faker\Factory as Faker;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\Gateway;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Response;

class GatewayTest extends TestCase
{
    /**
     * The billing / shipping info for customer info requests.
     *
     * @var array
     */
    protected $billing;

    /**
     * The customer info for customer info requests.
     *
     * @var array
     */
    protected $customer;

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

        $faker = Faker::create();
        $params = ['environment' => $this->environment];
        $this->gateway = Moneris::create($this->id, $this->token, $params);
        $this->params = [
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $this->billing = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'company_name' => $faker->company,
            'address' => $faker->streetAddress,
            'city' => $faker->city,
            'province' => 'SK',
            'postal_code' => 'X0X0X0',
            'country' => 'Canada',
            'phone_number' => '555-555-5555',
            'fax' => '555-555-5555',
            'tax1' => '1.01',
            'tax2' => '1.02',
            'tax3' => '1.03',
            'shipping_cost' => '9.99',
        ];
        $this->customer = [
            'email' => 'example@email.com',
            'instructions' => $faker->sentence(mt_rand(3, 6)),
            'billing' => $this->billing,
            'shipping' => $this->billing
        ];
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
        $response = $this->gateway->purchase($this->params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_make_a_purchase_with_provided_customer_information_and_receive_a_response()
    {
        $params = array_merge($this->params, [
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->gateway->purchase($params);
        $receipt = $response->receipt();

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
        $this->assertTrue($receipt->read('complete'));
        $this->assertNotNull($receipt->read('transaction'));
    }

    /** @test */
    public function it_can_make_a_cvd_secured_purchase_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'cvd' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->purchase($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_make_a_avs_secured_purchase_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'avs' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->purchase($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_pre_authorize_a_purchase_and_receive_a_response()
    {
        $response = $this->gateway->preauth($this->params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_pre_authorize_a_purchase_with_provided_customer_information_and_receive_a_response()
    {
        $params = array_merge($this->params, [
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->gateway->preauth($params);
        $receipt = $response->receipt();

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
        $this->assertTrue($receipt->read('complete'));
        $this->assertNotNull($receipt->read('transaction'));
    }

    /** @test */
    public function it_can_make_a_cvd_secured_pre_authorization_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'cvd' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->preauth($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_make_a_avs_secured_pre_authorization_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'avs' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->preauth($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_verify_a_card_before_attempting_a_purchase_and_receive_a_response()
    {
        $response = $this->gateway->verify($this->params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_verify_a_cvd_secured_card_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'cvd' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'cvd' => '111',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->verify($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_verify_a_avs_secured_card_and_receive_a_response()
    {
        $params = ['environment' => $this->environment, 'avs' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $params = [
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ];
        $response = $gateway->verify($params);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_void_a_transaction_after_making_a_purchase_and_receive_a_response()
    {
        $response = $this->gateway->purchase($this->params);
        $response = $this->gateway->void($response->transaction);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_refund_a_transaction_after_making_a_purchase_and_receive_a_response()
    {
        $response = $this->gateway->purchase($this->params);
        $response = $this->gateway->refund($response->transaction);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_capture_a_pre_authorized_transaction_and_receive_a_response()
    {
        $response = $this->gateway->preauth($this->params);
        $response = $this->gateway->capture($response->transaction);

        $this->assertEquals(Response::class, get_class($response));
        $this->assertTrue($response->successful);
    }

    /** @test */
    public function it_can_access_the_vault_functionality_to_handle_credit_card_data()
    {
        $vault = $this->gateway->cards();

        $this->assertEquals(Vault::class, get_class($vault));
        $this->assertObjectHasAttribute('id', $vault);
        $this->assertObjectHasAttribute('token', $vault);
        $this->assertObjectHasAttribute('environment', $vault);
    }
}