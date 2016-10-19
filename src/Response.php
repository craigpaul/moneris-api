<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Response
 *
 * @property null|int $status
 * @property bool $successful
 * @property \CraigPaul\Moneris\Transaction $transaction
 */
class Response
{
    use Gettable, Settable;

    const INVALID_TRANSACTION_DATA = 0;

    /**
     * The status code.
     *
     * @var null|int
     */
    protected $status = null;

    /**
     * Determines whether the response was successful.
     *
     * @var bool
     */
    protected $successful = true;

    /**
     * @var \CraigPaul\Moneris\Transaction
     */
    protected $transaction;

    /**
     * Create a new Response instance.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}