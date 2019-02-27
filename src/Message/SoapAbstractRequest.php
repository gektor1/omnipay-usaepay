<?php

namespace Omnipay\USAePay\Message;

use Exception;
use Guzzle;
use Guzzle\Http\ClientInterface;
use Guzzle\Common\Event;
use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * USAePay Abstract Request.
 *
 * This is the parent class for all Stripe requests.
 *
 * @see \Omnipay\Stripe\Gateway
 * @link https://wiki.usaepay.com/
 *
 * @method \Omnipay\USAePay\Message\Response send()
 */
abstract class SoapAbstractRequest extends OmnipayAbstractRequest
{
    protected $liveEndpoint = 'https://www.usaepay.com/soap/gate/0AE595C1/usaepay.wsdl';
    
    protected $sandboxEndpoint = 'https://sandbox.usaepay.com/soap/gate/0AE595C1/usaepay.wsdl';    
        
    /**
     * @var \stdClass The generated SOAP request, saved immediately before a transaction is run.
     */
    protected $request;
    /**
     * @var \stdClass The retrieved SOAP response, saved immediately after a transaction is run.
     */
    protected $response;
    
    protected $intervals = [
        '' => 'disabled',
        'day' => 'daily',
        'week' => 'weekly',
        'month' => 'monthly',
        'year' => 'annually',
    ];

    abstract public function getCommand();

    abstract public function getData();

    /**
     * Create a new Request
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->request = $this->createRequest();
    }
    
    public function getSandbox()
    {
        return $this->getParameter('sandbox');
    }

    public function setSandbox($value)
    {
        return $this->setParameter('sandbox', $value);
    }

    public function getSource()
    {
        return $this->getParameter('source');
    }

    public function setSource($value)
    {
        return $this->setParameter('source', $value);
    }

    public function getPin()
    {
        return $this->getParameter('pin');
    }

    public function setPin($value)
    {
        return $this->setParameter('pin', $value);
    }

    public function getInvoice()
    {
        return $this->getParameter('invoice');
    }

    public function setInvoice($value)
    {
        return $this->setParameter('invoice', $value);
    }

    public function getDescription()
    {
        return $this->getParameter('description');
    }

    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    public function getAddCustomer()
    {
        if ($this->getParameter('addCustomer') === true) {
            return 'yes';
        }

        return '';
    }

    public function setAddCustomer($value)
    {
        return $this->setParameter('addCustomer', $value);
    }

    public function getInterval()
    {
        $interval = $this->getParameter('interval');

        return $this->intervals[$interval];
    }

    public function setInterval($value)
    {
        if (empty($value)) {
            $value = '';
        }

        if (!in_array($value, array_keys($this->intervals))) {
            throw new Exception('Interval not in list of allowed values.');
        }

        return $this->setParameter('interval', $value);
    }

    public function getIntervalCount()
    {
        return $this->getParameter('intervalCount');
    }

    public function setIntervalCount($value)
    {
        return $this->setParameter('intervalCount', (int) $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }
    
    public function sendData($data)
    {
        try {
            $soap = new \SoapClient($this->getEndpoint());
        } catch (\SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }
        $command = $this->getCommand();
        $response = $soap->$command($this->getToken(), $this->request);
        return $this->response = new SoapResponse($this->request, $response);
    }
    
    protected function getEndpoint()
    {
        return $this->getSandbox() ? $this->sandboxEndpoint : $this->liveEndpoint;
    }
    
    /**
     * @return \stdClass
     */
    protected function createRequest()
    {
        // build the class for the request
        $request = new \stdClass();
        
        return $request;
    }
    
        /**
     * @return \stdClass
     */
    protected function createCard()
    {
        $creditCard = $this->getCard();

        $usaepayCreditCard = new \stdClass();
        $usaepayCreditCard->CardNumber = $creditCard->getNumber();
        $usaepayCreditCard->CardExpiration = $creditCard->getExpiryDate('my');

        if (!is_null($creditCard->getCvv())) {
            $usaepayCreditCard->CardCode = $creditCard->getCvv();
        }

        return $usaepayCreditCard;
    }
    
    /**
     * assembly ueSecurityToken as an array
     * 
     * @return array
     */
    public function getToken() {
        // generate random seed value
        $seed = time() . rand();
        // make hash value using sha1 function
        $hash = sha1($this->getSource() . $seed . $this->getPin());
        return [
            'SourceKey' => $this->getSource(),
            'PinHash' => array(
                'Type' => 'sha1',
                'Seed' => $seed,
                'HashValue' => $hash
            ),
            'ClientIP' => ''
        ];
    }

}
