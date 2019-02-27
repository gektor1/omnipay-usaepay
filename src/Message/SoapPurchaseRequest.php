<?php

namespace Omnipay\USAePay\Message;

/**
 * USAePay SOAP Purchase Request
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the USAePay Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('USAePay');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'testMode' => true
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
 *     echo "Authorize transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class SoapPurchaseRequest extends SoapAuthorizeRequest {

    public function getCommand() {
        return 'runSale';
    }

    public function sendData($data) {
        
        var_dump($data);exit;
        
        $this->request->AccountHolder = 'Tester Jones';

        $this->request->Details = new \stdClass();
        $this->request->Details->Description = 'Example Transaction';
        $this->request->Details->Amount = $this->getAmount();
        $this->request->Details->Invoice = $this->getParameter('invoice');
        $this->request->Details->Currency = $this->getParameter('currency');
        $this->request->CreditCardData = $this->createCard();

        return parent::sendData($data);
    }

}
