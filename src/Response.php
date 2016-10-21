<?php

namespace CraigPaul\Moneris;

use SimpleXMLElement;

/**
 * CraigPaul\Moneris\Response
 *
 * @property array $errors
 * @property bool $failedAvs
 * @property bool $failedCvd
 * @property null|int $status
 * @property bool $successful
 * @property \CraigPaul\Moneris\Transaction $transaction
 */
class Response
{
    use Gettable, Settable;

    const ERROR                    = -23;
    const INVALID_TRANSACTION_DATA = 0;

    const FAILED_ATTEMPT            = -1;
    const CREATE_TRANSACTION_RECORD = -2;
    const GLOBAL_ERROR_RECEIPT      = -3;

    const SYSTEM_UNAVAILABLE    = -14;
    const CARD_EXPIRED          = -15;
    const INVALID_CARD          = -16;
    const INSUFFICIENT_FUNDS    = -17;
    const PREAUTH_FULL          = -18;
    const DUPLICATE_TRANSACTION = -19;
    const DECLINED              = -20;
    const NOT_AUTHORIZED        = -21;

    const CVD               = -4;
    const CVD_NO_MATCH      = -5;
    const CVD_NOT_PROCESSED = -6;
    const CVD_MISSING       = -7;
    const CVD_NOT_SUPPORTED = -8;

    const AVS             = -9;
    const AVS_POSTAL_CODE = -10;
    const AVS_ADDRESS     = -11;
    const AVS_NO_MATCH    = -12;
    const AVS_TIMEOUT     = -13;

    const POST_FRAUD = -22;

    /**
     * Any errors that arise from processing a transaction.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Determine if we have failed Address Verification Service verification.
     *
     * @var bool
     */
    protected $failedAvs = false;

    /**
     * Determine if we have failed Card Validation Digits verification.
     *
     * @var bool
     */
    protected $failedCvd = false;

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
     * Retrieve the transaction's receipt if it is available.
     *
     * @return \SimpleXMLElement|\SimpleXMLElement[]|null
     */
    public function receipt()
    {
        if (!is_null($response = $this->transaction->response)) {
            return $response->receipt;
        }

        return null;
    }

    /**
     * Validate the response.
     *
     * @return $this
     */
    public function validate()
    {
        $receipt = $this->receipt();
        $gateway = $this->transaction->gateway;

        if ($receipt->ReceiptId === 'Global Error Receipt') {
            $this->status = Response::GLOBAL_ERROR_RECEIPT;
            $this->successful = false;

            return $this;
        }

        $code = (int)$receipt->ResponseCode;

        if ($code >= 50 || $code === 0) {
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

            $this->successful = false;

            return $this;
        }

        $code = isset($receipt->AvsResultCode) ? (string)$receipt->AvsResultCode : false;

        if ($gateway->avs && $code && $code !== 'null' && !in_array($code, $gateway->avsCodes)) {
            switch ($code) {
                case 'B':
                case 'C':
                    $this->status = Response::AVS_POSTAL_CODE;
                    break;
                case 'G':
                case 'I':
                case 'P':
                case 'S':
                case 'U':
                case 'Z':
                    $this->status = Response::AVS_ADDRESS;
                    break;
                case 'N':
                    $this->status = Response::AVS_NO_MATCH;
                    break;
                case 'R':
                    $this->status = Response::AVS_TIMEOUT;
                    break;
                default:
                    $this->status = Response::AVS;
            }

            $this->failedAvs = true;
            $this->successful = false;

            return $this;
        }

        $code = isset($receipt->CvdResultCode) ? (string)$receipt->CvdResultCode : null;

        if ($gateway->avs && !is_null($code) && $code !== 'null' && !in_array($code{1}, $gateway->cvdCodes)) {
            $this->status = Response::CVD;
            $this->failedCvd = true;
            $this->successful = false;

            return $this;
        }

        $this->successful = true;

        return $this;
    }
}