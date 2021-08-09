<?php

namespace org\gocdb\services;

require_once __DIR__ . '/../../../../lib/Gocdb_Services/Factory.php';
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
