<?php namespace Omnipay\Cielo\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $requestsEndpoint = 'https://api.cieloecommerce.cielo.com.br/';
    protected $queryEndpoint = 'https://apiquery.cieloecommerce.cielo.com.br/';
    protected $requestsEndpointTest = 'https://apisandbox.cieloecommerce.cielo.com.br';
    protected $queryEndpointTest = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
    protected $baseEndpoint = 'requests';
    protected $requestMethod = 'POST';

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

        //return $payload;
        return $this->response = $this->createResponse(@$payload);
    }

    protected function setBaseEndpoint($value)
    {
        $this->baseEndpoint = $value;
    }

    public function __get($name)
    {
        return $this->getParameter($name);
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

    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }
    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }
    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', $value);
    }
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }

    public function getCustomerName()
    {
        return $this->getParameter('customer_name');
    }

    public function setCustomerName($value)
    {
        $this->setParameter('customer_name', $value);
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
