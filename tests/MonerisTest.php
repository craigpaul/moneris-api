<?php

use CraigPaul\Moneris\Moneris;
use CraigPaul\Moneris\Gateway;

class MonerisTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Moneris store id.
     *
     * @var string
     */
    protected $id;

    /**
     * The Moneris API parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * The Moneris API token.
     *
     * @var string
     */
    protected $token;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->id = 'store1';
        $this->token = 'yesguy';
        $this->params = [
            'environment' => Moneris::ENV_TESTING
        ];
    }

    /** @test */
    public function it_can_instantiate_via_the_constructor()
    {
        $moneris = new Moneris($this->id, $this->token, $this->params);

        $this->assertEquals(Moneris::class, get_class($moneris));
        $this->assertObjectHasAttribute('id', $moneris);
        $this->assertObjectHasAttribute('token', $moneris);
        $this->assertObjectHasAttribute('environment', $moneris);
        $this->assertObjectHasAttribute('params', $moneris);
    }

    /** @test */
    public function it_can_instantiate_via_a_static_create_method()
    {
        $moneris = Moneris::create($this->id, $this->token, $this->params);

        $this->assertEquals(Moneris::class, get_class($moneris));
        $this->assertObjectHasAttribute('id', $moneris);
        $this->assertObjectHasAttribute('token', $moneris);
        $this->assertObjectHasAttribute('environment', $moneris);
        $this->assertObjectHasAttribute('params', $moneris);
    }

    /** @test */
    public function it_can_access_properties_of_the_class()
    {
        $moneris = new Moneris($this->id, $this->token, $this->params);

        $this->assertEquals($this->id, $moneris->id);
        $this->assertEquals($this->token, $moneris->token);
        $this->assertEquals($this->params['environment'], $moneris->environment);
        $this->assertEquals($this->params, $moneris->params);
    }

    /** @test */
    public function it_fails_to_retrieve_a_non_existent_property_of_the_class()
    {
        $moneris = new Moneris($this->id, $this->token, $this->params);

        $this->expectException(InvalidArgumentException::class);

        $moneris->nonExistentProperty;
    }

    /** @test */
    public function it_can_retrieve_the_gateway_for_moneris()
    {
        $moneris = new Moneris($this->id, $this->token, $this->params);

        $gateway = $moneris->gateway();

        $this->assertEquals(Gateway::class, get_class($gateway));
        $this->assertObjectHasAttribute('id', $gateway);
        $this->assertObjectHasAttribute('token', $gateway);
        $this->assertObjectHasAttribute('environment', $gateway);
    }
}