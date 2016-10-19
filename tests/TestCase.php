<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $amex;

    /**
     * @var string
     */
    protected $mastercard;

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
    }
}