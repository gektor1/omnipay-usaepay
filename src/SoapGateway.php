<?php

namespace Omnipay\USAePay;

use Omnipay\Common\AbstractGateway;

/**
 * USAePay SOAP Gateway
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the USAePay SOAP Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('USAePay_Soap');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'source' => 'MySource',
 *     'testMode' => false
 * ));
 *
 * // Create a credit card object
 * // This card can be used for testing.
 * $card = new CreditCard(array(
 *             'firstName'    => 'Example',
 *             'lastName'     => 'Customer',
 *             'number'       => '4242424242424242',
 *             'expiryMonth'  => '01',
 *             'expiryYear'   => '2020',
 *             'cvv'          => '123',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'amount'                   => '10.00',
 *     'currency'                 => 'USD',
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class SoapGateway extends AbstractGateway
{
    public function getName()
    {
        return 'USAePay.com';
    }
    
    public function getShortName()
    {
        return 'USAePay_Soap';
    }

    public function getDefaultParameters()
    {
        return array(
            'source' => '',
            'pin' => '',
            'testMode' => false,
            'sandbox' => false
        );
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

    /**
     * Create an authorize request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapAuthorizeRequest', $parameters);
    }

    /**
     * Capture request.
     *
     * Use this request to capture and process a previously created
     * authorization.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapCaptureRequest', $parameters);
    }

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapPurchaseRequest', $parameters);
    }

    /**
     * Refund Request.
     *
     * The 'refund' command allows the merchant to refund some or all of a
     * previous sale transaction. It can be used with both credit card and check
     * sales. It requires that the Transaction ID (refnum) of the original sale
     * be submitted in the UMrefNum field along with the amount to be refunded.
     * If the amount is not submitted, then the entire amount of the original
     * sale will be refunded. The refund command will work for both credit card
     * and check transactions. Not all check processors support refunds on
     * checks so Merchants should verify with their provider that they can use
     * this command.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapRefundRequest', $parameters);
    }

    /**
     * Void Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapVoidRequest', $parameters);
    }

    /**
     * Create Card.
     *
     * This call can be used to create a new customer or add a card
     * to an existing customer.
     *
     * @param array $parameters
     *
     * @return \Omnipay\USAePay\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapCreateCardRequest', $parameters);
    }

    /**
     * Create Subscription
     *
     * This call can be used to create a subscription (recurring payment).
     *
     * @param array $parameters
     * @return \Omnipay\USAePay\Message\CreateSubscriptionRequest
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapCreateSubscriptionRequest', $parameters);
    }
    
    /**
     * @param array $parameters
     * @return SoapQueryBatchResponse
     */
    public function queryBatch(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapQueryBatchRequest', $parameters);
    }
    
    /**
     * @param array $parameters
     * @return SoapQueryBatchDetailResponse
     */
    public function queryBatchDetail(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\USAePay\Message\SoapQueryBatchDetailRequest', $parameters);
    }
    
}
