<?php

use CraigPaul\Moneris\Customer;

class CustomerTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $customer = new Customer();

        $this->assertEquals(Customer::class, get_class($customer));
        $this->assertObjectHasAttribute('id', $customer);
        $this->assertObjectHasAttribute('email', $customer);
        $this->assertObjectHasAttribute('phone', $customer);
        $this->assertObjectHasAttribute('note', $customer);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $customer = Customer::create();

        $this->assertEquals(Customer::class, get_class($customer));
        $this->assertObjectHasAttribute('id', $customer);
        $this->assertObjectHasAttribute('email', $customer);
        $this->assertObjectHasAttribute('phone', $customer);
        $this->assertObjectHasAttribute('note', $customer);
    }
}