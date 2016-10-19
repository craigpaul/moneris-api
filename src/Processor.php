<?php

namespace CraigPaul\Moneris;

class Processor
{
    /**
     * Determine if the request is valid. If so, process the
     * transaction via the Moneris API.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     *
     * @return \CraigPaul\Moneris\Response
     */
    public static function process(Transaction $transaction)
    {
        if ($transaction->invalid()) {
            $response = new Response($transaction);
            $response->status = Response::INVALID_TRANSACTION_DATA;
            $response->successful = false;

            return $response;
        }

        return new Response($transaction);
    }
}