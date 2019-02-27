<?php

namespace Omnipay\USAePay\Message;

/**
 * USAePay Authorize Request
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
 * // Do an authorize transaction on the gateway
 * $transaction = $gateway->authorize(array(
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
class SoapAuthorizeRequest extends SoapAbstractRequest
{
        public function getData()
    {
        if (!is_null($this->getCard())) {
            $this->validate('amount', 'card');
            $this->getCard()->validate();
        } elseif (!is_null($this->getBankAccount())) {
            $this->validate('amount', 'bankAccount');
        } else {
            $this->validate('amount', 'bankAccount', 'card');
        }
        
        //Since we use soap we dont really use this. Just validate here.
        return array();
    }

    public function getCommand()
    {
        return 'runAuthOnly';
    }
}
