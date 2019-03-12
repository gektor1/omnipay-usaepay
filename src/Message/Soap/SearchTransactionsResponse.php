<?php

namespace Omnipay\USAePay\Message\Soap;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * USAePay Response
 *
 * This is the response class for all USAePay requests.
 *
 * @see \Omnipay\USAePay\Gateway
 */
class SearchTransactionsResponse extends AbstractResponse {

    public function __construct($request, $data) {
        $this->request = $request;

        $this->data = $this->decodeData($data);
    }

    public function decodeData($data) {
        return (array) $data;
    }

    public function isSuccessful() {
        return true;
    }

}
