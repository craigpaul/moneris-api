<?php

use CraigPaul\Moneris\Customer;

class CustomerTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $customer = new Customer();

        $this->assertEquals(Customer::class, get_class($customer));
        $this->assertObjectHasAttribute('data', $customer);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $customer = Customer::create();

        $this->assertEquals(Customer::class, get_class($customer));
        $this->assertObjectHasAttribute('data', $customer);
    }

    /** @test */
    public function it_can_access_customer_data_that_exists_on_the_class()
    {
        $params = [
            'id' => uniqid('customer-', true),
            'email' => 'example@email.com',
            'phone' => '555-555-5555',
            'note' => 'Customer note',
        ];
        $customer = Customer::create($params);

        $this->assertEquals($params['id'], $customer->id);
        $this->assertEquals($params['email'], $customer->email);
        $this->assertEquals($params['phone'], $customer->phone);
        $this->assertEquals($params['note'], $customer->note);
    }
}