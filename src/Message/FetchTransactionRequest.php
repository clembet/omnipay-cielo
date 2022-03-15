<?php namespace Omnipay\Cielo\Message;

//https://developercielo.github.io/manual/'?shell#consulta-paymentid
class FetchTransactionRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://apiquery.cieloecommerce.cielo.com.br/';
    protected $testEndpoint = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
    protected $requestMethod = 'GET';
    protected $resource = 'sales';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return [];
    }

    public function sendData($data)
    {
        $this->validate('transactionId');

        $url = $this->getEndpoint();

        $headers = [
            'MerchantId' => $this->getMerchantId(),
            'MerchantKey' => $this->getMerchantKey(),
            'Content-Type' => 'application/json'
        ];

        $url = sprintf(
            '%s/%s',
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $httpResponse = $this->httpClient->request($this->getMethod(), $url, $headers);
        $json = $httpResponse->getBody()->getContents();
        return $this->createResponse(@json_decode($json, true));
    }
}
