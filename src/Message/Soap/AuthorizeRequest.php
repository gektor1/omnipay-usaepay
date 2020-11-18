<?php

namespace Omnipay\USAePay\Message\Soap;

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
class AuthorizeRequest extends AbstractRequest {

    public function getData() {
        $data = $this->getBaseData();

        $this->validate('amount');
        if ($this->getCardReference()) {
            
        } else {
            if (!is_null($this->getCard())) {
                $this->validate('card');
                $this->getCard()->validate();
            } elseif (!is_null($this->getBankAccount())) {
                $this->validate('bankAccount');
            } else {
                $this->validate('bankAccount', 'card');
            }
        }

        $data->Command = $this->getCommand();

        $data->Details = new \stdClass();
        $data->Details->Description = $this->getDescription();
        $data->Details->Amount = $this->getAmount();
        $data->Details->Invoice = $this->getParameter('invoice');
        $data->Details->Currency = $this->getParameter('currency');

        if (!is_null($this->getCard())) {
            $data->AccountHolder = $this->getCard()->getFirstName() . ' ' . $this->getCard()->getLastName();

            $data->BillingAddress = new \stdClass();
            $data->BillingAddress->Email = $this->getCard()->getEmail();
            $data->BillingAddress->FirstName = $this->getCard()->getFirstName();
            $data->BillingAddress->LastName = $this->getCard()->getLastName();
            if ($this->getCard()->getPostcode())
                $data->BillingAddress->Zip = $this->getCard()->getPostcode();
            
            $data->CreditCardData = $this->createCard();
        } elseif (!is_null($this->getBankAccount())) {
            $data->AccountHolder = $this->getBankAccount()->getAccountHolder();

            $data->BillingAddress = new \stdClass();
            $data->BillingAddress->Email = $this->getBankAccount()->getEmail();
            $data->BillingAddress->FirstName = $this->getBankAccount()->getFirstName();
            $data->BillingAddress->LastName = $this->getBankAccount()->getLastName();
            if ($this->getBankAccount()->getPostcode())
                $data->BillingAddress->Zip = $this->getBankAccount()->getPostcode();
            
            $data->CheckData = $this->createBankAccount();
        }

        if ($this->getItems()) {
            $data->LineItems = [];

            foreach ($this->getItems() as $item) {
                $lineItem = new \stdClass();
                $lineItem->ProductName = $item->getName();
                $lineItem->Qty = $item->getQuantity();
                $lineItem->Description = $item->getDescription();
                $lineItem->UnitPrice = $item->getPrice();

                $data->LineItems[] = $lineItem;
            }
        }

        $data->CustReceipt = true;
        $data->CustomerID = $this->getCustomer();
        
        if ($this->getClientIp()) {
            $data->ClientIP = $this->getClientIp();
        }
        
        return $data;
    }
    
    public function getCommand() {
        return 'authonly';
    }

}
