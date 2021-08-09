<?php

namespace org\gocdb\services;

require_once __DIR__ . '/../../../lib/Gocdb_Services/Factory.php';
require_once __DIR__ . '/PIExtWriteRequest.php';
require_once __DIR__ . '/../resultReturnFunctions.php';

$userServ = \Factory::getUserService();
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (isset($_REQUEST['request'])) {
    $baseUrl = $_REQUEST['request'];
}
else {
    $baseUrl = null;
}

$requestContents = file_get_contents('php://input');

$piReq = new PIExtWriteRequest($userServ);
$returnArray = $piReq->processRequest($requestMethod, $baseUrl, $requestContents);

returnJsonWriteAPIResult($returnArray['httpResponseCode'], $returnArray['returnObject']);
/*
// New API key based authorization
function authAPIKey() {
    $gocdb_api_key = getenv("GOCDB_API_KEY");
    if ($gocdb_api_key == false)
        die("No GOCDB API key was set in the system.");

    if (strcmp($this->apiKey, $gocdb_api_key) != 0) {
        die("No valid API key was provided. An API key is required to access this resource.");
    }
}
*/
