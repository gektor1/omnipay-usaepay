<?php

namespace Omnipay\USAePay\Message;

/**
 * USAePay SOAP Search Batch Request
 *
 */
class SoapQueryBatchDetailRequest extends SoapAbstractRequest {

    public function getBatchRefNum()
    {
        return $this->getParameter('batchRefNum');
    }

    public function setBatchRefNum($value)
    {
        return $this->setParameter('batchRefNum', $value);
    }
    
    public function getCommand() {
        return 'getBatchTransactions';
    }

    public function getData() {
        
        $this->validate('batchRefNum');
        
        return [];
    }

    public function sendData($data) {
        
        try {
            $soap = new \SoapClient($this->getEndpoint());
        } catch (\SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }

        $response = $soap->getBatchTransactions($this->getToken(), $this->getBatchRefNum());

        return $this->response = new SoapQueryBatchDetailResponse($this->request, $response);
    }

}
