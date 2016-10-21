<?php

use CraigPaul\Moneris\Vault;
use CraigPaul\Moneris\CreditCard;

class VaultTest extends TestCase
{
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
        $card = CreditCard::create($this->visa, '2012');

        $response = $this->vault->add($card);
        $receipt = $response->receipt();

        $this->assertTrue($response->successful);
        $this->assertNotNull($receipt->DataKey);
    }
}