<?php

namespace Omnipay\USAePay\Message;

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
class SoapPurchaseRequest extends SoapAuthorizeRequest {

    public function getCommand() {
        if (!is_null($this->getCard())) {
            return 'runSale';
        } elseif (!is_null($this->getBankAccount())) {
            return 'runCheckSale';
        }
    }

    public function sendData($data) {
        $this->request->Details = new \stdClass();
        $this->request->Details->Description = $this->getDescription();
        $this->request->Details->Amount = $this->getAmount();
        $this->request->Details->Invoice = $this->getParameter('invoice');
        $this->request->Details->Currency = $this->getParameter('currency');

        if (!is_null($this->getCard())) {
            $this->request->AccountHolder = $this->getCard()->getFirstName() . ' ' . $this->getCard()->getLastName();
            
            $this->request->BillingAddress = new \stdClass();
            $this->request->BillingAddress->Email = $this->getCard()->getEmail();
            $this->request->BillingAddress->FirstName = $this->getCard()->getFirstName();
            $this->request->BillingAddress->LastName = $this->getCard()->getLastName();
            
            $this->request->CreditCardData = $this->createCard();
        } elseif (!is_null($this->getBankAccount())) {
            $this->request->AccountHolder = $this->getBankAccount()->getName();
            
            $this->request->BillingAddress = new \stdClass();
            $this->request->BillingAddress->Email = $this->getBankAccount()->getEmail();
            $this->request->BillingAddress->FirstName = $this->getBankAccount()->getFirstName();
            $this->request->BillingAddress->LastName = $this->getBankAccount()->getLastName();
            
            $this->request->CheckData = $this->createBankAccount();
        }

        if ($this->getItems()) {
            $this->request->LineItems = [];
            
            foreach ($this->getItems() as $item) {
                $lineItem = new \stdClass();
                $lineItem->ProductName = $item->getName();
                $lineItem->Qty = $item->getQuantity();
                $lineItem->Description = $item->getDescription();
                $lineItem->UnitPrice = $item->getPrice();
                
                $this->request->LineItems[] = $lineItem;
            }
        }
        
        $this->request->CustReceipt = true;
        
        return parent::sendData($data);
    }

}
