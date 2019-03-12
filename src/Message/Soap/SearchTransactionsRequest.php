<?php

namespace Omnipay\USAePay\Message\Soap;

class SearchTransactionsRequest extends AbstractRequest {

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

    public function getData() {

        $this->validate('start', 'end');

        $data = [
            [
                'Field' => 'checktrace.settled',
                'Type' => 'gt',
                'Value' => $this->getStart()->format('Y-m-d H:i:s')
            ],
            [
                'Field' => 'checktrace.settled',
                'Type' => 'lt',
                'Value' => $this->getEnd()->format('Y-m-d H:i:s')
            ]
        ];

        return $data;
    }

    public function getCommand() {
        return 'getCustomer';
    }

    public function sendData($data) {
        $soap = $this->getSoapClient();

        $this->request = $data;


        $response = $soap->searchTransactions($this->getToken(), $data, true, 0, 1000, 0);

        return $this->response = new SearchTransactionsResponse($data, $response);
    }

}
