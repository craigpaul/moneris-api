<?php

class VaultExpiringStub
{
    /**
     * Render the stub.
     *
     * @param array $cards
     *
     * @return string
     */
    public function render(array $cards)
    {
        $response = '<?xml version="1.0"?><response><receipt>';

        foreach ($cards as $card) {
            $receipt = $card->receipt();
            $data = $receipt->read('data');
            $expdate = $data['expiry_date']['year'].$data['expiry_date']['month'];

            $response .= '<ResolveData>';
            $response .= '<data_key>'.$receipt->read('key').'</data_key>';
            $response .= '<payment_type>cc</payment_type><cust_id></cust_id><phone></phone><email></email><note></note>';
            $response .= '<expdate>'.$expdate.'</expdate>';
            $response .= '<masked_pan>'.$data['masked_pan'].'</masked_pan>';
            $response .= '<crypt_type>'.$data['crypt'].'</crypt_type>';
            $response .= '</ResolveData>';
        }

        $response .= '<DataKey></DataKey><ReceiptId></ReceiptId><ReferenceNum></ReferenceNum>';
        $response .= '<ResponseCode>001</ResponseCode><ISO></ISO><AuthCode></AuthCode>';
        $response .= '<Message>Successfully located '.count($cards).' expiring cards.</Message>';
        $response .= '<TransTime></TransTime><TransDate>'.date('Y-m-d').'</TransDate>';
        $response .= '<TransType>'.date('h:i:s').'</TransType><Complete>true</Complete>';
        $response .= '<TransAmount></TransAmount><CardType></CardType><TransID></TransID><TimedOut></TimedOut>';
        $response .= '<CorporateCard></CorporateCard><RecurSuccess></RecurSuccess><AvsResultCode></AvsResultCode>';
        $response .= '<CvdResultCode></CvdResultCode><ResSuccess>true</ResSuccess><PaymentType></PaymentType>';
        $response .= '</receipt></response>';

        return $response;
    }
}