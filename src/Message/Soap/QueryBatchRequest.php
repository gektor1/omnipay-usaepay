<?php

namespace Omnipay\USAePay\Message\Soap;

/**
 * USAePay SOAP Search Batch Request
 *
 */
class QueryBatchRequest extends AbstractRequest {

    /**
     * 
     * @return \DateTime
     */
    public function getStart() {
        return $this->getParameter('start');
    }

    public function setStart($value) {
        return $this->setParameter('start', $value);
    }

    /**
     * 
     * @return \DateTime
     */
    public function getEnd() {
        return $this->getParameter('end');
    }

    public function setEnd($value) {
        return $this->setParameter('end', $value);
    }

    public function getCommand() {
        return 'searchBatches';
    }

    public function getData() {

        $this->validate('start', 'end');

        $data = [
            [
                'Field' => 'closed',
                'Type' => 'gt',
                'Value' => $this->getStart()->format('Y-m-d H:i:s')
            ], [
                'Field' => 'closed',
                'Type' => 'lt',
                'Value' => $this->getEnd()->format('Y-m-d H:i:s')
            ]
        ];

        return $data;
    }

    public function sendData($data) {

        try {
            $soap = new \SoapClient($this->getEndpoint());
        } catch (\SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }

        $response = $soap->searchBatches($this->getToken(), $data, true, 0, 100);

        return $this->response = new QueryBatchResponse($this->request, $response);
    }

}
