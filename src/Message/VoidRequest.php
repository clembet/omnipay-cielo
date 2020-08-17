<?php

namespace Omnipay\Cielo\Message;

class VoidRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $requestsEndpoint = 'https://api.cieloecommerce.cielo.com.br/';
    protected $queryEndpoint = 'https://apiquery.cieloecommerce.cielo.com.br/';
    protected $requestsEndpointTest = 'https://apisandbox.cieloecommerce.cielo.com.br';
    protected $queryEndpointTest = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
    protected $baseEndpoint = 'requests';
    protected $requestMethod = 'PUT';

    public function sendData($data)
    {
        $transactionId = $this->getTransactionId();
        $method = $this->requestMethod;
        $url = $this->getRequestUrl($data)."$transactionId/void";
        print "$url\n";

        $headers = [
            'MerchantId' => $this->getMerchantId(),
            'MerchantKey' => $this->getMerchantKey(),
            'Content-Type' => 'application/json'
        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            $headers
        );

        $payload = $this->decode($response->getBody());
        $payload['Payment']['PaymentId'] = $transactionId;

        return $this->response = $this->createResponse(@$payload);
    }

    public function getData()
    {
        $data = [];

        return $data;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    public function getMerchantKey()
    {
        return $this->getParameter('merchant_key');
    }

    public function setMerchantKey($value)
    {
        return $this->setParameter('merchant_key', $value);
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    private function getRequestUrl($data)
    {
        $baseUrl = ($this->baseEndpoint === 'requests') ? ($this->getTestMode()?$this->requestsEndpointTest:$this->requestsEndpoint) : ($this->getTestMode()?$this->queryEndpointTest:$this->queryEndpoint);

        return $baseUrl . '/1/sales/';
    }
}
