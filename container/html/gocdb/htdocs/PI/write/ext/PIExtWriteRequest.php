<?php
namespace org\gocdb\services;

date_default_timezone_set("UTC");

class PIExtWriteRequest {
    private $supportedAPIVersions = array("v1");
    private $supportedRequestMethods = array("POST", "PUT", "DELETE");
    private $supportedEntityTypes = array("user");

    private $userService;

    private $returnCode = 500;
    private $returnObject = null;

    public function __construct($userServ) {
        $this->userService = $userServ;
    }

    public function processRequest($method, $requestUrl, $requestContents) {
        try {
            // Put all relevant request data/information into a single associative array
            $request = $this->processRequestData($method, $requestUrl, $requestContents);

            // Authentication is required for this API
            $this->authAPIKey($request);

            // Verify checks that:
            // - URL is in the form /<apiVersion>/<entityType>/
            // - the request method is supported
            // - the entity type is known
            $this->verifyRequestData($request);

            // TODO: Process request
            $this->returnObject = $request;
            $this->returnCode = 200;
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $errorArray["Error"]= array("Code" => $this->returnCode, "Message" => utf8_encode($message));
            $this->returnObject = $errorArray;
        }

        return array("httpResponseCode" => $this->returnCode, "returnObject" => $this->returnObject);
    }

    private function exceptionWithResponseCode($code, $message) {
        $this->returnCode = $code;
        throw new \Exception($message);
    }

    private function processRequestData($method, $requestUrl, $requestContents) {
        $request["Method"] = $method;

        $requestArray = explode("/", $requestUrl);
        $requestArray = array_filter($requestArray, "strlen");

        if (count($requestArray) != 2) {
            $this->exceptionWithResponseCode(400, "invalid API URL length");
        }

        $request["APIVersion"] = $requestArray[0];
        $request["EntityType"] = $requestArray[1];

        if (!empty($requestContents)) {
            $data = json_decode($requestContents, true);

            if (array_key_exists("APIKey", $data)) {
                $request["APIKey"] = $data["APIKey"];
            }

            if (array_key_exists("Payload", "data")) {
                $request["Data"] = $data["Payload"];
            }
        } else {
            $this->exceptionWithResponseCode(400, "content missing");
        }

        return $request;
    }

    private function verifyRequestData($request) {
        if (!in_array($request["APIVersion"], $this->supportedAPIVersions)) {
            $this->exceptionWithResponseCode(400, "unsupported API version");
        }

        if (!in_array($request["Method"], $this->supportedRequestMethods)) {
            $this->exceptionWithResponseCode(400, "unsupported request method");
        }

        if (!in_array($request["EntityType"], $this->supportedEntityTypes)) {
            $this->exceptionWithResponseCode(400, "unsupported entity type");
        }
    }
    
    private function authAPIKey($request) {
        $gocdb_api_key = getenv("GOCDB_API_KEY");
        if ($gocdb_api_key == false) {
            $this->exceptionWithResponseCode(500, "no GOCDB API key was set in the system");
        }

        if (array_key_exists("APIKey", $request)) {
            $apiKey = $request["APIKey"];
            if (strcmp($apiKey, $gocdb_api_key) != 0) {
                $this->exceptionWithResponseCode(401, "no valid API key was provided");
            }
        } else {
            $this->exceptionWithResponseCode(401, "no API key was provided");
        }
    }
}
