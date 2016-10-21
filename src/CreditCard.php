<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\CreditCard
 *
 * @property-read int $crypt
 * @property-read \CraigPaul\Moneris\Customer|null $customer
 * @property-read string $expiry
 * @property-read string $number
 */
class CreditCard
{
    use Gettable;

    /**
     * @var int
     */
    protected $crypt;

    /**
     * @var \CraigPaul\Moneris\Customer|null
     */
    protected $customer = null;

    /**
     * @var string
     */
    protected $expiry;

    /**
     * @var string
     */
    protected $number;

    /**
     * Create a new CreditCard instance.
     *
     * @param string $number
     * @param string $expiry
     * @param int $crypt
     *
     * @return void
     */
    public function __construct(string $number, string $expiry, int $crypt = 7)
    {
        $this->number = $number;
        $this->expiry = $expiry;
        $this->crypt = $crypt;
    }

    /**
     * Attach a provided customer to the CreditCard instance.
     *
     * @param \CraigPaul\Moneris\Customer $customer
     *
     * @return $this
     */
    public function attach(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Create a new CreditCard instance.
     *
     * @param string $number
     * @param string $expiry
     * @param int $crypt
     *
     * @return $this
     */
    public static function create(string $number, string $expiry, int $crypt = 7)
    {
        return new static($number, $expiry, $crypt);
    }
}