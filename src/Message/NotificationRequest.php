<?php namespace Omnipay\Cielo\Message;

//https://developercielo.github.io/manual/cielo-ecommerce#post-de-notifica%C3%A7%C3%A3o
/*
 A URL que recebe o POST deve pegar as seguintes variaveis
         {
           "RecurrentPaymentId": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
           "PaymentId": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
           "ChangeType": "2"
        }



CHANGETYPE	DESCRIÇÃO
“1”	Mudança de status do pagamento.
“2”	Recorrência criada.
“3”	Mudança de status do Antifraude.
“4”	Mudança de status do pagamento recorrente (Ex.: desativação automática).
“5”	Estorno negado (aplicável para Rede).
“6”	Boleto registrado pago a menor.
“7”	Notificação de chargeback. Para mais detalhes, consulte o manual de Risk Notification.
“8”	Alerta de fraude.

 */
class NotificationRequest extends AbstractRequest //TODO: refazer
{
    protected $resource = 'notifications';
    protected $requestMethod = 'GET';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return parent::getData();
    }

    public function getNotificationType()
    {
        return $this->getParameter('notificationType');
    }

    public function setNotificationType($value)
    {
        return $this->setParameter('notificationType', $value);
    }

    public function setNotificationCode($value)
    {
        return $this->setParameter('notificationCode', $value);
    }

    public function getNotificationCode()
    {
        return $this->getParameter('notificationCode');
    }

    public function sendData($data)
    {
        $this->validate('notificationCode');

        $url = sprintf(
            '%s/%s?%s',
            $this->getEndpoint(),
            $this->getNotificationCode(),
            http_build_query($data, '', '&')
        );

        print $url."\n\n";
        $httpResponse = $this->httpClient->request($this->getMethod(), $url, ['Content-Type' => 'application/x-www-form-urlencoded']);
        $xml          = @simplexml_load_string($httpResponse->getBody()->getContents(), 'SimpleXMLElement', LIBXML_NOCDATA);

        return $this->createResponse(@$this->xml2array($xml));
    }
}
