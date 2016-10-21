<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\CreditCard
 *
 * @property-read string $expiry
 * @property-read string $number
 * @property-read int $crypt
 */
class CreditCard
{
    use Gettable;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $expiry;

    /**
     * @var int
     */
    protected $crypt;

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