<?php namespace Omnipay\Cielo\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Pagarme Response
 *
 * This is the response class for all Pagarme requests.
 *
 * @see \Omnipay\Pagarme\Gateway
 */
class Response extends AbstractResponse
{
    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if(isset($this->data['error']) || isset($this->data['error_messages']))
            return false;
        /*if (isset($this->data['Payment']['Status']) && isset($this->data['Payment']['ReasonCode']))
            if($this->data['Payment']['ReasonCode']==0)
                return true;*/


        $result = $this->data;
        if(isset($this->data['Payment']['ReturnCode']))
            $result = $this->data['Payment'];

        $ReturnCode = @$result['ReturnCode'];
        $status = @$result['Status'];

        if(($ReturnCode==0) && (($status==1)||($status==2)||($status==10)||($status==11)||($status==12)))
            return true;

        return false;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionID()
    {
        if ($this->isSuccessful()) {
            if(isset($this->data['Payment']['PaymentId']))
                return @$this->data['Payment']['PaymentId'];
        }

        return null;
    }

    public function getTransactionAuthorizationCode()
    {
        if ($this->isSuccessful()) {
            $result = $this->data;
            if(isset($this->data['Payment']))
                $result = $this->data['Payment'];
            return @$result['AuthorizationCode'];
        }

        return null;
    }

    public function getStatus() // https://developercielo.github.io/manual/'?shell#status-transacional
    {
        $status = null;
        if(isset($this->data['Payment']['Status']))
            $status = @$this->data['Payment']['Status'];
        else
        {
            if(isset($this->data['Status']))
                $status = @$this->data['Status'];
        }

        return $status;
    }

    public function isPaid()
    {
        $status = $this->getStatus();
        return $status==2;
    }

    public function isAuthorized()
    {
        $status = $this->getStatus();
        return $status==1;
    }

    public function isPending()
    {
        $status = $this->getStatus();
        return $status==12;
    }

    public function isVoided()
    {
        $status = $this->getStatus();
        return ($status==10||$status==11);
    }


    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            $result = $this->data;
            if(isset($this->data['Payment']))
                $result = $this->data['Payment'];

            return @$result['ReturnMessage'];
        }

        return null;
    }

    public function getBoleto()
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['boleto_url'] = @$data['Payment']['Url'];
        $boleto['boleto_url_pdf'] = @$data['Payment']['Url'];
        $boleto['boleto_barcode'] = @$data['Payment']['DigitableLine'];
        $boleto['boleto_expiration_date'] = @$data['Payment']['ExpirationDate'];
        $boleto['boleto_valor'] = (@$data['Payment']['Amount']*1.0)/100.0;
        $boleto['boleto_transaction_id'] = @$data['Payment']['PaymentId'];
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix()
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['pix_qrcodebase64image'] = @$data['Payment']['QrcodeBase64Image'];
        $boleto['pix_qrcodestring'] = @$data['Payment']['QrCodeString'];
        $boleto['pix_valor'] = (@$data['Payment']['Amount']*1.0)/100.0;
        $boleto['pix_transaction_id'] = @$data['Payment']['Paymentid'];
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }
}