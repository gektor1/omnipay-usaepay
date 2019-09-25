<?php

namespace Omnipay\USAePay\Message\Soap;

class UpdateCardPaymentMethodRequest extends AbstractRequest {

    public function getData() {
        $data = $this->getBaseData();

        $data = $this->createCard();

        $data->MethodName = 'ARP';
        $data->SecondarySort = 1;
        $data->MethodID = $this->getPaymentMethod();
        
        $data->CardNumber = 'XXXXXXXXXXXX' . $data->CardNumber; 

        return $data;
    }

    public function getCommand() {
        return 'addCustomer';
    }

    public function sendData($data) {
        $soap = $this->getSoapClient();

        $this->request = $data;

        return $soap->updateCustomerPaymentMethod($this->getToken(), $data, false);
    }

}
