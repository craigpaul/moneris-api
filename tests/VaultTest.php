<?php

use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\CreditCard;

class VaultTest extends TestCase
{
    /**
     * @var \CraigPaul\Moneris\CreditCard
     */
    protected $card;

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

        $this->card = CreditCard::create($this->visa, '2012');
        $this->params = [
            'order_id' => uniqid('1234-567890', true),
            'amount' => '1.00',
        ];
        $this->vault = Vault::create($this->id, $this->token, $this->environment);
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
    public function it_can_peek_into_the_vault_and_retrieve_a_full_credit_card_from_the_moneris_vault_with_a_valid_data_key()
    {
        $response = $this->vault->add($this->card);
        $key = $response->receipt()->read('key');

        $response = $this->vault->peek($key, true);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->read('key'));
        $this->assertEquals('12', $receipt->read('data')['expiry_date']['month']);
        $this->assertEquals('20', $receipt->read('data')['expiry_date']['year']);
        $this->assertEquals($this->visa, $receipt->read('data')['pan']);
    }

    /** @test */
    public function it_can_retrieve_all_expiring_credit_cards_from_the_moneris_vault()
    {
        $expiry = date('ym', strtotime('today + 10 days'));

        $card = CreditCard::create($this->visa, $expiry);
        $this->vault->add($card);
        $card = CreditCard::create($this->mastercard, $expiry);
        $this->vault->add($card);
        $card = CreditCard::create($this->amex, $expiry);
        $this->vault->add($card);

        $response = $this->vault->expiring();
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertGreaterThan(0, count($receipt->read('data')));
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
}