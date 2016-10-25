<?php

namespace CraigPaul\Moneris;

use GuzzleHttp\Client;

class Processor
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * API configuration.
     *
     * @var array
     */
    protected $config = [
        'protocol' => 'https',
        'host' => 'esqa.moneris.com',
        'port' => '443',
        'url' => '/gateway2/servlet/MpgRequest',
        'api_version' => 'PHP - 2.5.6',
        'timeout' => 60,
    ];

    /**
     * Global error response to maintain consistency.
     *
     * @var string
     */
    protected $error = "<?xml version=\"1.0\"?><response><receipt><ReceiptId>Global Error Receipt</ReceiptId><ReferenceNum>null</ReferenceNum><ResponseCode>null</ResponseCode><ISO>null</ISO> <AuthCode>null</AuthCode><TransTime>null</TransTime><TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete><Message>null</Message><TransAmount>null</TransAmount><CardType>null</CardType><TransID>null</TransID><TimedOut>null</TimedOut></receipt></response>";

    /**
     * Create a new Processor instance.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve the API configuration.
     *
     * @param string $environment
     *
     * @return array
     */
    public function config($environment = '')
    {
        if ($environment === Moneris::ENV_LIVE) {
            $this->config['host'] = 'www3.moneris.com';
        }

        return $this->config;
    }

    /**
     * Determine if the request is valid. If so, process the
     * transaction via the Moneris API.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function process(Transaction $transaction)
    {
        if ($transaction->invalid()) {
            $response = new Response($transaction);
            $response->status = Response::INVALID_TRANSACTION_DATA;
            $response->successful = false;
            $response->errors = $transaction->errors;

            return $response;
        }

        $response = $this->submit($transaction);

        return $transaction->validate($response);
    }

    /**
     * Parse the global error response stub.
     *
     * @return \SimpleXMLElement
     */
    protected function error()
    {
        return simplexml_load_string($this->error);
    }

    /**
     * Set up and send the request to the Moneris API.
     *
     * @param array $config
     * @param string $url
     * @param string $xml
     *
     * @return string
     */
    protected function send(array $config, $url = '', $xml = '')
    {
        $response = $this->client->post($url, [
            'body' => $xml,
            'headers' => [
                'User-Agent' => $config['api_version']
            ],
            'timeout' => $config['timeout']
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Submit the transaction to the Moneris API.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     *
     * @return \SimpleXMLElement|string
     */
    protected function submit(Transaction $transaction)
    {
        $config = $this->config($transaction->gateway->environment);

        $url = $config['protocol'].'://'.$config['host'].':'.$config['port'].$config['url'];

        $xml = str_replace(' </', '</', $transaction->toXml());

        $response = $this->send($config, $url, $xml);

        if (!$response) {
            return $this->error();
        }

        $response = @simplexml_load_string($response);

        if ($response === false) {
            return $this->error();
        }

        return $response;
    }
}
