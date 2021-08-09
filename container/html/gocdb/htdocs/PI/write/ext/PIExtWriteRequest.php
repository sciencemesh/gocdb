<?php
namespace org\gocdb\services;

date_default_timezone_set("UTC");

class PIExtWriteRequest {
    private $userService;

    private $returnCode = 500;
    private $returnObject = null;

    public function __construct($userServ) {
        $this->$userService = $userServ;
    }

    public function processRequest($method, $requestUrl, $requestContents) {
        try {
            // TODO: Process request
            $obj["method"] = $method;
            $obj["reqURL"] = $requestUrl;
            $obj["contents"] = $requestContents;
            $this->returnObject = $obj;
            $this->$returnCode = 200;

        } catch (\Exception $e) {
            $message = $e->getMessage();

            $errorArray['Error']= array('Code' => $this->$returnCode, 'Message' => utf8_encode($message));
            $this->returnObject = $errorArray;
        }

        return array(
            'httpResponseCode' => $this->$returnCode,
            'returnObject' => $this->returnObject
        );
    }
}
