<?php

namespace Omnipay\Cielo\Message;

//https://developercielo.github.io/manual/'?shell#cancelamento-total
class VoidRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $resource = 'sales';
    protected $requestMethod = 'PUT';

    public function getData()
    {
        $this->validate('transactionId', 'amount');
        //$data = parent::getData();
        $data = [];

        return $data;
    }

    public function sendData($data)
    {
        $this->validate('transactionId', 'amount');

        $url = $this->getEndpoint();

        $headers = [
            'MerchantId' => $this->getMerchantId(),
            'MerchantKey' => $this->getMerchantKey(),
            'Content-Type' => 'application/json',
        ];

        $url = sprintf(
            "%s/%s/void?amount=%d",
            $this->getEndpoint(),
            $this->getTransactionID(),
            (int)($this->getAmount()*100.0)
        );

        //print_r([$this->getMethod(), $url, $headers]);exit();
        $httpResponse = $this->httpClient->request($this->getMethod(), $url, $headers);
        $json = @json_decode($httpResponse->getBody()->getContents(), true);
        //@$json['Payment']['PaymentId'] = $this->getTransactionID();
        return $this->createResponse($json);
    }
}
