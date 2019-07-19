<?php

use CraigPaul\Moneris\Moneris;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $amex;

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
    protected $mastercard;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $visa;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->amex = '373599005095005';
        $this->mastercard = '5454545454545454';
        $this->visa = '4242424242424242';

        $this->id = 'store2';
        $this->token = 'yesguy';
        $this->environment = Moneris::ENV_TESTING;
    }
}
