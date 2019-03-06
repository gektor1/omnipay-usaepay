<?php

namespace Omnipay\USAePay\Message\Soap;

/**
 * USAePay Create Card Request
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
 * // Do a create card transaction on the gateway
 * $transaction = $gateway->createCard(array(
 *     'amount'                   => '1.00',
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
class CreateCardRequest extends AbstractRequest {

    public function getData() {
        $data = $this->getBaseData();

        $this->validate('amount');
        if ($this->getCustomer()) {
            
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

        $data->BillingAddress = new \stdClass();
        
        if (!is_null($this->getCard())) {
            $data->BillingAddress->FirstName = $this->getCard()->getFirstName();
            $data->BillingAddress->LastName = $this->getCard()->getLastName();
            $data->BillingAddress->Email = $this->getCard()->getEmail();
            
            $paymentMethod = $this->createCard();
        } elseif (!is_null($this->getBankAccount())) {
            $data->BillingAddress->FirstName = $this->getBankAccount()->getFirstName();
            $data->BillingAddress->LastName = $this->getBankAccount()->getLastName();
            $data->BillingAddress->Email = $this->getBankAccount()->getEmail();
            
            $paymentMethod = $this->createBankAccount();
        }
        $paymentMethod->MethodName = 'ARP';
        $paymentMethod->SecondarySort = 1;
        
        $data->PaymentMethods = [$paymentMethod];
        
        $data->CustomerID = $this->getCustomer();
        $data->Description = '';
        $data->Enabled = false;
        $data->Amount = $this->getAmount();
        $data->Next = '';
        $data->NumLeft = -1;
        $data->OrderID = '';
        $data->ReceiptNote = '';
        $data->Schedule = '';
        $data->SendReceipt = true;
        $data->Source = '';
                
        return $data;
    }

    public function getCommand() {
        return 'addCustomer';
    }

    public function sendData($data) {
        $soap = $this->getSoapClient();

        $this->request = $data;

        $response = $soap->addCustomer($this->getToken(), $data);

        return $this->response = new CreateCardResponse($data, $response);
    }

}
