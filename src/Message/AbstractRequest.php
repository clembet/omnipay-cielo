<?php namespace Omnipay\Cielo\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://api.cieloecommerce.cielo.com.br/';
    protected $liveEndpointConsultas = 'https://apiquery.cieloecommerce.cielo.com.br/';
    protected $testEndpoint = 'https://apisandbox.cieloecommerce.cielo.com.br';
    protected $testEndpointConsultas = 'https://apiquerysandbox.cieloecommerce.cielo.com.br/';
    protected $version = 1;
    protected $requestMethod = 'POST';
    protected $resource = 'sales';

    public function getData()
    {
        $this->validate('merchant_id', 'merchant_key');

        return [
        ];
    }
    
    public function sendData($data)
    {
        $method = $this->requestMethod;
        //$url = $this->getRequestUrl($data);
        $url = $this->getEndpoint();

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

    public function getPaymentProvider()
    {
        return $this->getParameter('paymentProvider');
    }

    public function setPaymentProvider($value)
    {
        $this->setParameter('paymentProvider', $value);
    }

    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        $this->setParameter('paymentType', $value);
    }

    public function getDueDate()
    {
        $dueDate = $this->getParameter('dueDate');
        if($dueDate)
            return $dueDate;

        $time = localtime(time());
        $ano = $time[5]+1900;
        $mes = $time[4]+1+1;
        $dia = 1;// $time[3];
        if($mes>12)
        {
            $mes=1;
            ++$ano;
        }

        $dueDate = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
        $this->setDueDate($dueDate);

        return $dueDate;
    }

    public function setDueDate($value)
    {
        return $this->setParameter('dueDate', $value);
    }

    public function getTransactionID()
    {
        return $this->getParameter('transactionId');
    }

    public function setTransactionID($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getMethod()
    {
        return $this->requestMethod;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function getEndpoint()
    {
        $version = $this->getVersion();
        $endPoint = ($this->getTestMode()?$this->testEndpoint:$this->liveEndpoint);
        return  "{$endPoint}/{$version}/{$this->getResource()}";
    }

    public function toJSON($data, $options = 0)
    {
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomerData()
    {
        $card = $this->getCard();
        $customer = $this->getCustomer();

        $data = [
            "Name"=>$customer->getName(),
            "Identity"=>$customer->getDocumentNumber(),
            "IdentityType"=>"CPF",
            "Email"=>$customer->getEmail(),
            "Birthdate"=>$customer->getBirthday('Y-m-d'),// formato ISO
            "IpAddress"=>$this->getClientIp(),
            "Address"=>[
                "Street"=>$customer->getBillingAddress1(),
                "Number"=>$customer->getBillingNumber(),
                "Complement"=>$customer->getBillingAddress2(),
                "ZipCode"=>$customer->getBillingPostcode(),
                "District"=>$customer->getBillingDistrict(),
                "City"=>$customer->getBillingCity(),
                "State"=>$customer->getBillingState(),
                "Country"=>"BRA"
            ],
        ];

        if(strcmp(strtolower($this->getPaymentType()), "creditcard")==0)
        {
            $data["DeliveryAddress"]=[
                "Street"=>$card->getShippingAddress1(),
                "Number"=>$card->getShippingNumber(),
                "Complement"=>$card->getShippingAddress2(),
                "ZipCode"=>$card->getShippingPostcode(),
                "District"=>$card->getShippingDistrict(),
                "City"=>$card->getShippingCity(),
                "State"=>$card->getShippingState(),
                "Country"=>"BRA"
            ];
        }

        return $data;
    }

    public function getItemData()
    {
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {
                $item_array = [];
                $item_array['id'] = $n+1;
                $item_array['title'] = $item->getName();
                $item_array['description'] = $item->getName();
                //$item_array['category_id'] = $item->getCategoryId();
                $item_array['quantity'] = (int)$item->getQuantity();
                //$item_array['currency_id'] = $this->getCurrency();
                $item_array['unit_price'] = (double)($this->formatCurrency($item->getPrice()));

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    public function getDataCreditCard()//https://developercielo.github.io/manual/cielo-ecommerce#cart%C3%A3o-de-cr%C3%A9dito
    {
        $this->validate('card');
        $card = $this->getCard();

        $expiryMonth = str_pad($card->getExpiryMonth(), 2, 0, STR_PAD_LEFT);
        $expiryYear = $card->getExpiryYear();

        $data = [
            "MerchantOrderId" => $this->getOrderId(),
            "Customer"        => $this->getCustomerData(),
            "Payment"         => [
                "Type"           => "CreditCard",
                "Currency"       =>"BRL",
                "Interest"       =>"ByMerchant",
                "Capture"        => true,
                "ServiceTaxAmount"=>0,
                "Authenticate"   =>false,
                "Amount"         => $this->getAmountInteger(),
                "Installments"   => $this->getInstallments(),
                "SoftDescriptor" => $this->getSoftDescriptor(),
                "CreditCard"     => [
                    "CardNumber"     => $card->getNumber(),
                    "Holder"         => $card->getName(),
                    "ExpirationDate" => $expiryMonth . '/' . $expiryYear,
                    "SecurityCode"   => $card->getCvv(),
                    "SaveCard"       =>"false",
                    "Brand"          => $card->getBrand()
                ]
            ]
        ];

        return $data;
    }

    public function getDataBoleto() //https://developercielo.github.io/manual/cielo-ecommerce#boleto
    {
        $this->validate('paymentProvider');
        $customer = $this->getCustomerData();
        unset($customer["DeliveryAddress"]);

        $data = [
            "MerchantOrderId"=>$this->getOrderId(),
            "Customer"        => $customer,
            "Payment"=>[
                "Provider"=>$this->getTestMode()?"Simulado":$this->getPaymentProvider(), // ver lista de providers: [Bradesco2, BancoDoBrasil2] https://developercielo.github.io/manual/'?shell#regras-adicionais
                "Type"=>"Boleto",
                "Amount"=>$this->getAmountInteger(),
                //"BoletoNumber"=>$this->getOrderId(),
                //"Assignor"=> $this->getSoftDescriptor(),
                //"Demonstrative"=> "Compra em ".$this->getSoftDescriptor(),
                "ExpirationDate"=> $this->getDueDate(),
                //"Identification"=> "CNPJ do cedente",
                "Instructions"=> "Aceitar somente até a data de vencimento.",
                //"DaysToFine"=> 1,  // só para bradesco
                //"FineRate"=> 10.00000,// só para bradesco
                //"FineAmount"=> 1000,// só para bradesco
                //"DaysToInterest"=> 1,// só para bradesco
                //"InterestRate"=> 0.00000,// só para bradesco
                //"InterestAmount"=> 0,// só para bradesco
                //"DiscountAmount"=> 0,// só para bradesco
                //"DiscountLimitDate"=> "2017-12-31",// só para bradesco
                //"DiscountRate"=> 0.00000// só para bradesco
            ]
        ];

        return $data;
    }

    public function getDataPix()//https://developercielo.github.io/manual/cielo-ecommerce#pix
    {
        $customer = $this->getCustomerData();
        unset($customer["Address"]);
        unset($customer["DeliveryAddress"]);

        $data = [
            "MerchantOrderId" => $this->getOrderId(),
            "Customer"        => $customer,
            "Payment"         => [
                "Type"           => "Pix",
                "Amount"         => $this->getAmountInteger()
            ]
        ];

        return $data;
    }
}
