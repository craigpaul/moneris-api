<?php

namespace CraigPaul\Moneris;

use SimpleXMLElement;

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

    const ERROR                    = -23;
    const INVALID_TRANSACTION_DATA = 0;

    const GLOBAL_ERROR_RECEIPT = -3;

    const SYSTEM_UNAVAILABLE    = -14;
    const CARD_EXPIRED          = -15;
    const INVALID_CARD          = -16;
    const INSUFFICIENT_FUNDS    = -17;
    const PREAUTH_FULL          = -18;
    const DUPLICATE_TRANSACTION = -19;
    const DECLINED              = -20;
    const NOT_AUTHORIZED        = -21;

    const CVD = -4;

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

    /**
     * Create a new Response instance.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     *
     * @return $this
     */
    public static function create(Transaction $transaction)
    {
        return new static($transaction);
    }

    /**
     * @param SimpleXMLElement $receipt
     */
    protected function handle(SimpleXMLElement $receipt)
    {
        switch ($receipt->ResponseCode) {
            case '050':
            case '074':
            case 'null':
                $this->status = Response::SYSTEM_UNAVAILABLE;
                break;
            case '051':
            case '482':
            case '484':
                $this->status = Response::CARD_EXPIRED;
                break;
            case '075':
                $this->status = Response::INVALID_CARD;
                break;
            case '076':
            case '079':
            case '080':
            case '081':
            case '082':
            case '083':
                $this->status = Response::INSUFFICIENT_FUNDS;
                break;
            case '077':
                $this->status = Response::PREAUTH_FULL;
                break;
            case '078':
                $this->status = Response::DUPLICATE_TRANSACTION;
                break;
            case '481':
            case '483':
                $this->status = Response::DECLINED;
                break;
            case '485':
                $this->status = Response::NOT_AUTHORIZED;
                break;
            case '486':
            case '487':
            case '489':
            case '490':
                $this->status = Response::CVD;
                break;
            default:
                $this->status = Response::ERROR;
        }
    }

    /**
     * Validate the response.
     *
     * @return $this
     */
    public function validate()
    {
        /** @var \SimpleXMLElement $receipt */
        $receipt = $this->transaction->response->receipt;

        if ($receipt->ReceiptId === 'Global Error Receipt') {
            $this->status = Response::GLOBAL_ERROR_RECEIPT;
            $this->successful = false;

            return $this;
        }

        $code = (int)$receipt->ResponseCode;

        if ($code >= 50 || $code === 0) {
            $this->handle($receipt);
            $this->successful = false;

            return $this;
        }

        $this->successful = true;

        return $this;
    }
}