<?php

namespace Omnipay\Cielo\Requests;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $requestsEndpoint = 'https://apisandbox.cieloecommerce.cielo.com.br';
    protected $queryEndpoint = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
    protected $baseEndpoint = 'requests';
    protected $requestMethod = 'POST';

    public function initialize(array $parameters = array())
    {
        if (isset($parameters['environment']) && $parameters['environment'] === 'production') {
            $this->requestsEndpoint = 'https://api.cieloecommerce.cielo.com.br/';
            $this->queryEndpoint = 'https://apiquery.cieloecommerce.cielo.com.br/';
        }

        return parent::initialize($parameters);
    }

    public function sendData($data)
    {
        $method = $this->requestMethod;
        $url = $this->getRequestUrl($data);

        $headers = [
            'MerchantId' => $this->getMerchantId(),
            'MerchantKey' => $this->getMerchantKey(),
            'Content-Type' => 'application/json'
        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            json_encode($data)
        );

        $payload = $this->decode($response->getBody());

        return $payload;
    }

    protected function setBaseEndpoint($value)
    {
        $this->baseEndpoint = $value;
    }

    private function getRequestUrl($data)
    {
        $baseUrl = ($this->baseEndpoint === 'requests') ? $this->requestsEndpoint : $this->queryEndpoint;

        return $baseUrl . '/1/sales/';
    }

    protected function setRequestMethod($value)
    {
        return $this->requestMethod = $value;
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

    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }

    public function setEnvironment($value)
    {
        return $this->setParameter('environment', $value);
    }
}
