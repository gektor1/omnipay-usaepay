<?php

namespace Omnipay\USAePay\Message\Soap;

use Omnipay\Common\Message\AbstractResponse;

/**
 * USAePay SOAP Response
 *
 * This is the response class for all USAePay requests.
 *
 * @see \Omnipay\USAePay\Gateway
 */
class Response extends AbstractResponse {

    public function __construct($request, $data) {
        $this->request = $request;
        $this->data = $this->decodeData($data);
    }

    public function decodeData($data) {
        return $data;
    }

    public function isSuccessful() {
        return $this->data->ResultCode == "A";
    }

    public function getAuthorizationCode() {
//        return isset($this->data['UMauthCode']) ? $this->data['UMauthCode'] : null;
    }

    public function getTransactionId() {
//        return isset($this->data['UMrefNum']) ? $this->data['UMrefNum'] : null;
    }

    public function getTransactionReference() {
        return isset($this->data->RefNum) ? $this->data->RefNum : null;
    }

    public function getMessage() {
//        return isset($this->data['UMerror']) ? $this->data['UMerror'] : null;
    }

    public function getCardReferenceToken() {
//        return isset($this->data['UMcardRef']) ? $this->data['UMcardRef'] : null;
    }

    public function getCardType() {
//        return isset($this->data['UMcardType']) ? $this->data['UMcardType'] : null;
    }

    public function getMaskedCardNumber() {
//        return isset($this->data['UMmaskedCardNum']) ? $this->data['UMmaskedCardNum'] : null;
    }

    public function getCustomerReference() {
//        return isset($this->data['UMcustnum']) ? $this->data['UMcustnum'] : null;
    }

}
