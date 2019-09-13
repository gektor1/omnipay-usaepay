<?php

namespace Omnipay\USAePay\Message\Soap;

//use Exception;
//use Guzzle;
use Guzzle\Http\ClientInterface;
//use Guzzle\Common\Event;
use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\USAePay\BankAccount;

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
abstract class AbstractRequest extends OmnipayAbstractRequest {

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

    abstract public function getCommand();

    abstract public function getData();
//
//    /**
//     * Create a new Request
//     *
//     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
//     * @param HttpRequest     $httpRequest A Symfony HTTP request object
//     */
//    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest) {
//        parent::__construct($httpClient, $httpRequest);
//        $this->request = $this->createRequest();
//    }

    public function getSandbox() {
        return $this->getParameter('sandbox');
    }

    public function setSandbox($value) {
        return $this->setParameter('sandbox', $value);
    }

    public function getSource() {
        return $this->getParameter('source');
    }

    public function setSource($value) {
        return $this->setParameter('source', $value);
    }

    public function getPin() {
        return $this->getParameter('pin');
    }

    public function setPin($value) {
        return $this->setParameter('pin', $value);
    }

    public function getInvoice() {
        return $this->getParameter('invoice');
    }

    public function setInvoice($value) {
        return $this->setParameter('invoice', $value);
    }

    public function getDescription() {
        return $this->getParameter('description');
    }

    public function setDescription($value) {
        return $this->setParameter('description', $value);
    }

    public function getCustomer() {
        return $this->getParameter('customer');
    }

    public function setCustomer($value) {
        return $this->setParameter('customer', $value);
    }
    
    public function getPaymentMethod() {
        return $this->getParameter('paymentmethod');
    }

    public function setPaymentMethod($value) {
        return $this->setParameter('paymentmethod', $value);
    }

    public function sendData($data) {
        $soap = $this->getSoapClient();
        
        $this->request = $data;

        $response = $soap->runTransaction($this->getToken(), $data);
        
        return $this->response = new Response($data, $response);
    }
    
    public function getSoapClient() {
        try {
            return new \SoapClient($this->getEndpoint());
        } catch (\SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }
    }

    protected function getEndpoint() {
        return $this->getSandbox() ? $this->sandboxEndpoint : $this->liveEndpoint;
    }
//
//    /**
//     * @return \stdClass
//     */
//    protected function createRequest() {
//        $request = new \stdClass();
//        
//        $request->Command = $this->getCommand();
//        
//        return $request;
//    }

    /**
     * @return \stdClass
     */
    public function getBaseData()
    {
        $data = new \stdClass();
        return $data;
    }
    
    /**
     * @return \stdClass
     */
    protected function createCard() {
        $creditCard = $this->getCard();

        $usaepayCreditCard = new \stdClass();
        $usaepayCreditCard->CardNumber = $creditCard->getNumber();
        $usaepayCreditCard->CardExpiration = $creditCard->getExpiryDate('my');

        if (!is_null($creditCard->getCvv())) {
            $usaepayCreditCard->CardCode = $creditCard->getCvv();
        }

        if (!empty($creditCard->getPostcode())) {
            $usaepayCreditCard->AvsZip = $creditCard->getPostcode();
        }
        if (!empty($creditCard->getAddress1())) {
            $usaepayCreditCard->AvsStreet = $creditCard->getAddress1();
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

    public function setBankAccount($value)
    {
        if ($value && !$value instanceof BankAccount) {
            $value = new BankAccount($value);
        }
        return $this->setParameter('bankAccount', $value);
    }
    
    /**
     * @return BankAccount
     */
    public function getBankAccount() {
        return $this->getParameter('bankAccount');
    }

    /**
     * @return \stdClass
     */
    protected function createBankAccount() {
        /** @var BankAccount $bankAccount */
        $bankAccount = $this->getBankAccount();
        
        $usaepayBankAccount = new \stdClass();
        
        $usaepayBankAccount->Account = $bankAccount->getAccountNumber();
        $usaepayBankAccount->AccountType = $bankAccount->getBankAccountType();
        $usaepayBankAccount->Routing = $bankAccount->getRoutingNumber();
        
        return $usaepayBankAccount;
    }

}
