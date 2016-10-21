<?php

use CraigPaul\Moneris\Crypt;
use CraigPaul\Moneris\Customer;
use CraigPaul\Moneris\CreditCard;

class CreditCardTest extends TestCase
{
    /**
     * @var \CraigPaul\Moneris\CreditCard
     */
    protected $card;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->card = CreditCard::create($this->visa, '2012');
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $card = new CreditCard($this->visa, '2012', Crypt::SSL_ENABLED_MERCHANT);

        $this->assertEquals(CreditCard::class, get_class($card));
        $this->assertObjectHasAttribute('number', $card);
        $this->assertObjectHasAttribute('expiry', $card);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $card = CreditCard::create($this->visa, '2012', Crypt::SSL_ENABLED_MERCHANT);

        $this->assertEquals(CreditCard::class, get_class($card));
        $this->assertObjectHasAttribute('number', $card);
        $this->assertObjectHasAttribute('expiry', $card);
    }

    /** @test */
    public function it_can_attach_a_customer_to_a_credit_card()
    {
        $customer = Customer::create();

        $this->card->attach($customer);

        $this->assertEquals($customer, $this->card->customer);
    }
}