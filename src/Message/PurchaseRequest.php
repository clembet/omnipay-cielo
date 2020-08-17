<?php

namespace Omnipay\Cielo\Message;

class PurchaseRequest extends AuthorizeRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();

        $data["Payment"]["Capture"] = "true";
        $data["Payment"]["Authenticate"] = "false";

        return $data;
    }
}
