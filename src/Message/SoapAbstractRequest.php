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
/*
    public function sendData($data)
    {
        // check if we are mocking a request
        $mock = false;

        $listeners = $this->httpClient->getEventDispatcher()->getListeners('request.before_send');
        foreach ($listeners as $listener) {
            if (get_class($listener[0]) === 'Guzzle\Plugin\Mock\MockPlugin') {
                $mock = true;

                break;
            }
        }

        // if we are mocking, use guzzle, otherwise use umTransaction
        if ($mock) {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                null,
                $data
            );

            $httpResponse = $httpRequest->send();
        } else {
            $umTransaction = new umTransaction();
            $umTransaction->usesandbox = $this->getSandbox();
            $umTransaction->testmode = $this->getTestMode();
            $umTransaction->key = $this->getSource();
            $umTransaction->pin = $this->getPin();
            $umTransaction->command = $this->getCommand();
            $umTransaction->invoice = $this->getInvoice();
            $umTransaction->amount = $data['amount'];
            $umTransaction->description = $this->getDescription();
            $umTransaction->addcustomer = $this->getAddCustomer();
            $umTransaction->schedule = $this->getInterval();
            $umTransaction->numleft = $this->getIntervalCount();
            $umTransaction->start = 'next';

            if (isset($data['card'])) {
                $umTransaction->card = $this->getCard()->getNumber();
                $umTransaction->exp = $this->getCard()->getExpiryDate('my');
                $umTransaction->cvv2 = $this->getCard()->getCvv();
                $umTransaction->cardholder = $this->getCard()->getName();
                $umTransaction->street = $this->getCard()->getAddress1();
                $umTransaction->zip = $this->getCard()->getPostcode();
                $umTransaction->email = $this->getCard()->getEmail();

                $umTransaction->billfname = $this->getCard()->getBillingFirstName();
                $umTransaction->billlname = $this->getCard()->getBillingLastName();
                $umTransaction->billcompany = $this->getCard()->getBillingCompany();
                $umTransaction->billstreet = $this->getCard()->getBillingAddress1();
                $umTransaction->billstreet2 = $this->getCard()->getBillingAddress2();
                $umTransaction->billcity = $this->getCard()->getBillingCity();
                $umTransaction->billstate = $this->getCard()->getBillingState();
                $umTransaction->billzip = $this->getCard()->getBillingPostcode();
                $umTransaction->billcountry = $this->getCard()->getBillingCountry();
                $umTransaction->billphone = $this->getCard()->getBillingPhone();
            } elseif ($this->getCardReference()) {
                $umTransaction->card = $this->getCardReference();
                $umTransaction->exp = '0000';
            } else {
                $umTransaction->refnum = $this->getTransactionReference();
            }

            $processResult = $umTransaction->Process();

            if ($processResult !== true) {
                throw new Exception($umTransaction->error);
            }

            $httpResponse = Guzzle\Http\Message\Response::fromMessage($umTransaction->rawresult);
        }

        return $this->response = new Response($this, $httpResponse->getBody());
    }
*/
    
    public function sendData($data)
    {
        /*$data = $this->getData();
        $this->request->merchantReferenceCode = $this->getMerchantReferenceCode();
        $this->request->merchantID = $this->getMerchantId();
        $context_options = array(
            'http' => array(
                'timeout' => $this->timeout,
            ),
        );
        $context = stream_context_create($context_options);
        // options we pass into the soap client
        $soap_options = array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,        // turn on HTTP compression
            'encoding' => 'utf-8',        // set the internal character encoding to avoid random conversions
            'exceptions' => true,        // throw SoapFault exceptions when there is an error
            'connection_timeout' => $this->timeout,
            'stream_context' => $context,
        );
        // if we're in test mode, don't cache the wsdl
        if ($this->getTestMode()) {
            $soap_options['cache_wsdl'] = WSDL_CACHE_NONE;
        } else {
            $soap_options['cache_wsdl'] = WSDL_CACHE_BOTH;
        }*/
        try {
            // create the soap client
            $soap = new \SoapClient($this->getEndpoint()/*, $soap_options*/);
        } catch (SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }
        
        $command = $this->getCommand();
        
        // save the request so you can get back what was generated at any point
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
        $sourcekey = '_1uzA1u14K91dDk66Q9wj271LJlJ74Ff';
        $pin = 'test';

        // generate random seed value
        $seed = time() . rand();

        // make hash value using sha1 function
        $clear = $sourcekey . $seed . $pin;
        $hash = sha1($clear);

        $token = array(
            'SourceKey' => $sourcekey,
            'PinHash' => array(
                'Type' => 'sha1',
                'Seed' => $seed,
                'HashValue' => $hash
            ),
            'ClientIP' => $this->getIP()
        );
        return $token;
    }

    public function getIP() {
        $ch = curl_init("http://icanhazip.com/");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            return "ERROR";
        } else {
            return trim($result);
        }
    }
}
