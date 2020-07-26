<?php

namespace Omnipay\Cielo\Requests;

class AuthorizeRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('amount');

        $expiryMonth = str_pad($this->getCard()->getExpiryMonth(), 2, 0, STR_PAD_LEFT);
        $expiryYear = $this->getCard()->getExpiryYear();

        // @todo detect which type is
        $data = [
            "Amount "          => $this->getAmountInteger(),
            "MerchantOrderId" => $this->order_id,
            "Customer"        => [
                "Name" => $this->customer_name
            ],
            "Payment"         => [
                "Type"           => "CreditCard",
                "Amount"         => $this->getAmountInteger(),
                "Installments"   => $this->installments,
                "SoftDescriptor" => $this->soft_descriptor,
                "CreditCard"     => [
                    "CardNumber"     => $this->getCard()->getNumber(),
                    "Holder"         => $this->getCard()->getName(),
                    "ExpirationDate" => $expiryMonth . '/' . $expiryYear,
                    "SecurityCode"   => $this->getCard()->getCvv(),
                    "Brand"          => $this->getCard()->getBrand()
                ]
            ]
        ];

        return $data;
    }

    public function __get($name)
    {
        return $this->getParameter($name);
    }

    public function setOrderId($value)
    {
        $this->setParameter('order_id', $value);
    }

    public function setInstallments($value)
    {
        $this->setParameter('installments', $value);
    }

    public function setSoftDescriptor($value)
    {
        $this->setParameter('soft_descriptor', $value);
    }

    public function setCustomerName($value)
    {
        $this->setParameter('customer_name', $value);
    }
}
