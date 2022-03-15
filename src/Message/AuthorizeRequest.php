<?php

namespace Omnipay\Cielo\Message;

class AuthorizeRequest extends AbstractRequest
{
    protected $resource = 'sales';
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('customer', 'amount', 'paymentType');

        $data = [];
        switch(strtolower($this->getPaymentType()))
        {
            case 'creditcard':
                $data = $this->getDataCreditCard();
                $data["Payment"]["Capture"] = "false";
                break;

            default:
                $data = $this->getDataCreditCard();
        }

        return $data;
    }
}
