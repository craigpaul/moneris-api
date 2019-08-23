<?php

use Faker\Factory as Faker;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\Processor;
use CraigPaul\Moneris\CreditCard;
use CraigPaul\Moneris\Transaction;

class VaultTest extends TestCase
{
    /**
     * The billing / shipping info for customer info requests.
     *
     * @var array
     */
    protected $billing;

    /**
     * @var \CraigPaul\Moneris\CreditCard
     */
    protected $card;

    /**
     * The customer info for customer info requests.
     *
     * @var array
     */
    protected $customer;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var \CraigPaul\Moneris\Vault
     */
    protected $vault;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $faker = Faker::create();
        $this->card = CreditCard::create($this->visa, '2012');
        $this->params = [
            'order_id' => uniqid('1234-567890', true),
            'amount' => '1.00',
        ];
        $this->vault = Vault::create($this->id, $this->token, $this->environment);
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
        $this->items = [
            [
                'name' => $faker->sentence(mt_rand(3, 6)),
                'quantity' => '1',
                'product_code' => $faker->isbn10,
                'extended_amount' => $faker->randomFloat(2, 0.01, 999.99),
            ],
            [
                'name' => $faker->sentence(mt_rand(3, 6)),
                'quantity' => '1',
                'product_code' => $faker->isbn10,
                'extended_amount' => $faker->randomFloat(2, 0.01, 999.99),
            ]
        ];
        $this->customer = [
            'email' => 'example@email.com',
            'instructions' => $faker->sentence(mt_rand(3, 6)),
            'billing' => $this->billing,
            'shipping' => $this->billing,
            'items' => $this->items,
        ];
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $vault = new Vault($this->id, $this->token, $this->environment);

        $this->assertEquals(Vault::class, get_class($vault));
        $this->assertObjectHasAttribute('id', $vault);
        $this->assertObjectHasAttribute('token', $vault);
        $this->assertObjectHasAttribute('environment', $vault);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $vault = Vault::create($this->id, $this->token, $this->environment);

        $this->assertEquals(Vault::class, get_class($vault));
        $this->assertObjectHasAttribute('id', $vault);
        $this->assertObjectHasAttribute('token', $vault);
        $this->assertObjectHasAttribute('environment', $vault);
    }

    /** @test */
    public function it_can_add_a_credit_card_to_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $response = $this->vault->add($this->card);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
    }

    /** @test */
    public function it_can_add_a_credit_card_with_an_attached_customer_to_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $params = [
            'id' => uniqid('customer-', true),
            'email' => 'example@email.com',
            'phone' => '555-555-5555',
            'note' => 'Customer note',
        ];
        $customer = Customer::create($params);
        $card = $this->card->attach($customer);

        $response = $this->vault->add($card);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals($params['id'], $receipt->read('data')['customer_id']);
        $this->assertEquals($params['phone'], $receipt->read('data')['phone']);
        $this->assertEquals($params['email'], $receipt->read('data')['email']);
        $this->assertEquals($params['note'], $receipt->read('data')['note']);
    }

    /** @test */
    public function it_can_update_a_credit_card_in_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $this->assertEquals('2012', $response->transaction->params['expdate']);

        $this->card->expiry = '2112';

        $response = $this->vault->update($key, $this->card);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals('2112', $response->transaction->params['expdate']);
    }

    /** @test */
    public function it_can_update_a_credit_card_with_an_attached_customer_to_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $params = [
            'id' => uniqid('customer-', true),
            'email' => 'example@email.com',
            'phone' => '555-555-5555',
            'note' => 'Customer note',
        ];
        $customer = Customer::create($params);
        $card = $this->card->attach($customer);

        $response = $this->vault->add($card);
        $key = $response->receipt()->read('key');

        $this->card->customer->email = 'example2@email.com';

        $response = $this->vault->update($key, $this->card);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals('example2@email.com', $receipt->read('data')['email']);
    }

    /** @test */
    public function it_can_delete_a_credit_card_from_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $response = $this->vault->delete($key);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals($key, $receipt->read('key'));
    }

    /** @test */
    public function it_can_tokenize_a_previous_transaction_to_add_the_transactions_credit_card_in_the_moneris_vault_and_returns_a_data_key_for_storage()
    {
        $gateway = Moneris::create($this->id, $this->token, ['environment' => Moneris::ENV_TESTING]);

        $response = $gateway->purchase([
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
        ]);

        $response = $this->vault->tokenize($response->transaction);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
    }

    /** @test */
    public function it_can_peek_into_the_vault_and_retrieve_a_masked_credit_card_from_the_moneris_vault_with_a_valid_data_key()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $response = $this->vault->peek($key);
        $receipt = $response->receipt();
        $beginning = substr($this->visa, 0, 4);
        $end = substr($this->visa, -4, 4);

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals('12', $receipt->read('data')['expiry_date']['month']);
        $this->assertEquals('20', $receipt->read('data')['expiry_date']['year']);
        $this->assertEquals($beginning, substr($receipt->read('data')['masked_pan'], 0, 4));
        $this->assertEquals($end, substr($receipt->read('data')['masked_pan'], -4, 4));
    }

    /** @test */
    public function it_can_retrieve_all_expiring_credit_cards_from_the_moneris_vault()
    {
        $expiry = date('ym', strtotime('today + 10 days'));
        $cards = [];

        $card = CreditCard::create($this->visa, $expiry);
        $cards[] = $this->vault->add($card);
        $card = CreditCard::create($this->mastercard, $expiry);
        $cards[] = $this->vault->add($card);
        $card = CreditCard::create($this->amex, $expiry);
        $cards[] = $this->vault->add($card);

        $client = mock_handler((new VaultExpiringStub())->render($cards));

        $params = ['type' => 'res_get_expiring'];
        $transaction = new Transaction($this->vault, $params);
        $this->vault->transaction = $transaction;
        $processor = new Processor($client);

        $response = $processor->process($transaction);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertGreaterThan(0, count($receipt->read('data')));

        /** @var \CraigPaul\Moneris\Response $card */
        foreach ($cards as $index => $card) {
            /** @var \CraigPaul\Moneris\Receipt $rec */
            $rec = $card->receipt();

            $this->assertEquals($rec->read('key'), $receipt->read('data')[$index]['data_key']);
            $this->assertEquals($rec->read('data')['masked_pan'], $receipt->read('data')[$index]['masked_pan']);
        }
    }

    /** @test */
    public function it_can_make_a_purchase_with_a_credit_card_stored_in_the_moneris_vault()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
        ]);

        $response = $this->vault->purchase($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_make_a_purchase_with_a_credit_card_stored_in_the_moneris_vault_and_attach_customer_info()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->vault->purchase($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_submit_a_cvd_secured_purchase_with_a_credit_card_stored_in_the_moneris_vault()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'cvd' => true];
        $vault = Moneris::create($this->id, $this->token, $params)->cards();

        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'cvd' => '111',
        ]);

        $response = $vault->purchase($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_submit_an_avs_secured_purchase_with_a_credit_card_stored_in_the_moneris_vault()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'avs' => true];
        $vault = Moneris::create($this->id, $this->token, $params)->cards();

        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
        ]);

        $response = $vault->purchase($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_pre_authorize_a_credit_card_stored_in_the_moneris_vault()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
        ]);

        $response = $this->vault->preauth($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_pre_authorize_a_credit_card_stored_in_the_moneris_vault_and_attach_customer_info()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'cust_id' => uniqid('customer-', true),
            'cust_info' => $this->customer,
        ]);

        $response = $this->vault->preauth($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_submit_a_cvd_secured_pre_authorization_request_for_a_credit_card_stored_in_the_moneris_vault()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'cvd' => true];
        $vault = Moneris::create($this->id, $this->token, $params)->cards();

        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'cvd' => '111',
        ]);

        $response = $vault->preauth($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_submit_an_avs_secured_pre_authorization_request_for_a_credit_card_stored_in_the_moneris_vault()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'avs' => true];
        $vault = Moneris::create($this->id, $this->token, $params)->cards();

        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'avs_street_number' => '123',
            'avs_street_name' => 'Fake Street',
            'avs_zipcode' => 'X0X0X0',
        ]);

        $response = $vault->preauth($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_capture_a_pre_authorized_credit_card_stored_in_the_moneris_vault()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
        ]);

        $response = $this->vault->preauth($params);
        $response = $this->vault->capture($response->transaction);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals(true, $receipt->read('complete'));
    }

    /** @test */
    public function it_can_make_a_purchase_for_a_credit_card_stored_in_the_moneris_vault_using_credential_on_file()
    {
        $params = ['environment' => Moneris::ENV_TESTING, 'cof' => true];
        $gateway = Moneris::create($this->id, $this->token, $params);
        $vault = $gateway->cards();

        $preauth_params = [
            'order_id' => uniqid('1234-56789', true),
            'amount' => '1.00',
            'credit_card' => $this->visa,
            'expdate' => '2012',
            'payment_indicator' => 'C',
            'payment_information' => '0'
        ];

        $response = $gateway->preauth($preauth_params);
        $issuer_id = $response->receipt()->read('issuer_id');
        $response = $vault->add($this->card, ['issuer_id' => $issuer_id]);
        $key = $response->receipt()->read('key');

        $params = array_merge($this->params, [
            'data_key' => $key,
            'payment_indicator' => 'U',
            'payment_information' => '2',
            'issuer_id' => $issuer_id
        ]);

        $response = $vault->purchase($params);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertEquals($key, $receipt->read('key'));
        $this->assertEquals(true, $receipt->read('complete'));
        $this->assertNotEmpty($receipt->read('issuer_id'));
    }
}
