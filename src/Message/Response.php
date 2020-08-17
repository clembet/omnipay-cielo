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
        $result = $this->data;
        if(isset($this->data['Payment']['ReturnCode']))
            $result = $this->data['Payment'];

        $ReturnCode = @$result['ReturnCode'];
        $Status = @$result['Status'];
        if (($ReturnCode==4)||($ReturnCode==6))
            return true;

        if (($Status==10)&&($ReturnCode==0))
            return true;

        return false;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
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
}