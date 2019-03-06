<?php

namespace Omnipay\USAePay\Message\Soap;

/**
 * USAePay SOAP Purchase Request
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
class PurchaseCustomerRequest extends AuthorizeRequest {

    public function getCommand() {
        if (!is_null($this->getCard())) {
            return 'sale';
        } elseif (!is_null($this->getBankAccount())) {
            return 'check';
        }
    }

    public function sendData($data) {
        $soap = $this->getSoapClient();
        
        $this->request = $data;

        $response = $soap->runCustomerTransaction($this->getToken(), $this->getCardReference(), 0, $data);
        
        return $this->response = new Response($data, $response);
    }
    
}
