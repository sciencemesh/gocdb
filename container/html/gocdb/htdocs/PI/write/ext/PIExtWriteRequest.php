<?php
namespace org\gocdb\services;

require_once __DIR__ . '/UserRequest.php';

date_default_timezone_set("UTC");

/**
 * The basic JSON content of a POST/PUT request looks as follows:
 *  - APIKey
 *  - Operation
 *  - Data
**/

const KEY_OPERATION = "Operation";
const KEY_ENTITY_TYPE = "EntityType";
const KEY_APIKEY = "APIKey";
const KEY_APIVERSION = "APIVersion";
const KEY_METHOD = "Method";
const KEY_DATA = "Data";

const ENTITYTYPE_USER = "user";

const OPERATION_CREATEORUPDATE = "CreateOrUpdate";
const OPERATION_DELETE = "Delete";

class PIExtWriteRequest {
    private $supportedAPIVersions = array("v1");
    private $supportedRequestMethods = array("POST", "PUT");
    private $supportedEntityTypes = array(ENTITYTYPE_USER);

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

            // Handle the actual request; this will throw an exception in case of an error, so no exception means that everything went fine
            $obj = $this->handleRequest($request[KEY_ENTITY_TYPE], $request[KEY_OPERATION], $request[KEY_DATA]);
            $this->returnCode = 200;
            $this->returnObject = $obj;
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
        $request[KEY_METHOD] = $method;

        $requestArray = explode("/", $requestUrl);
        $requestArray = array_filter($requestArray, "strlen");

        if (count($requestArray) != 2) {
            $this->exceptionWithResponseCode(400, "invalid API URL length");
        }

        $request[KEY_APIVERSION] = $requestArray[0];
        $request[KEY_ENTITY_TYPE] = $requestArray[1];
        $request[KEY_OPERATION] = OPERATION_CREATEORUPDATE; // Default operation

        if (!empty($requestContents)) {
            $data = json_decode($requestContents, true);

            if (array_key_exists(KEY_APIKEY, $data)) {
                $request[KEY_APIKEY] = $data[KEY_APIKEY];
            }

            if (array_key_exists(KEY_OPERATION, $data)) {
                $request[KEY_OPERATION] = $data[KEY_OPERATION];
            }

            if (array_key_exists(KEY_DATA, $data)) {
                $request[KEY_DATA] = $data[KEY_DATA];
            }
        } else {
            $this->exceptionWithResponseCode(400, "content missing");
        }

        return $request;
    }

    private function verifyRequestData($request) {
        if (!in_array($request[KEY_APIVERSION], $this->supportedAPIVersions)) {
            $this->exceptionWithResponseCode(400, "unsupported API version");
        }

        if (!in_array($request[KEY_METHOD], $this->supportedRequestMethods)) {
            $this->exceptionWithResponseCode(405, "unsupported request method");
        }

        if (!in_array($request[KEY_ENTITY_TYPE], $this->supportedEntityTypes)) {
            $this->exceptionWithResponseCode(400, "unsupported entity type");
        }
    }

    private function authAPIKey($request) {
        $gocdb_api_key = getenv("GOCDB_API_KEY");
        if ($gocdb_api_key == false) {
            $this->exceptionWithResponseCode(500, "no GOCDB API key was set in the system");
        }

        if (array_key_exists(KEY_APIKEY, $request)) {
            $apiKey = $request[KEY_APIKEY];
            if (strcmp($apiKey, $gocdb_api_key) != 0) {
                $this->exceptionWithResponseCode(401, "no valid API key was provided");
            }
        } else {
            $this->exceptionWithResponseCode(401, "no API key was provided");
        }
    }

    private function handleRequest($entityType, $operation, $data) {
        try {
            switch ($entityType) {
            case ENTITYTYPE_USER:
                return $this->handleUserRequest($operation, $data);
            }
        } catch (\Exception $e) {
            $this->returnCode = 400;
            throw $e;
        }

        return null;
    }

    private function handleUserRequest($operation, $data) {
        $userReq = new UserRequest($this->userService);
        $reply = null;

        switch ($operation) {
        case OPERATION_CREATEORUPDATE:
            $reply = $userReq->createOrUpdateUser($data);
            break;

        case OPERATION_DELETE:
            $reply = $userReq->deleteUser($data);
            break;

        default:
            $this->exceptionWithResponseCode(400, "unsupported operation " . $operation);
        }

        return $reply;
    }
}
