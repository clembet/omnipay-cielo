<?php

namespace Omnipay\Cielo\Message;

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
            "MerchantOrderId" => $this->getOrderId(),
            "Customer"        => [
                "Name" => $this->getCustomerName()
            ],
            "Payment"         => [
                "Type"           => "CreditCard",
                "Amount"         => $this->getAmountInteger(),
                "Installments"   => $this->getInstallments(),
                "SoftDescriptor" => $this->getSoftDescriptor(),
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
}
