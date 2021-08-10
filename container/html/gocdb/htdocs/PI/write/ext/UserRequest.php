<?php
namespace org\gocdb\services;

/**
 * Data must be provided as JSON with the following fields:
 *  - Email
 *  - Title
 *  - FirstName
 *  - LastName
 *  - PhoneNumber
**/

class UserRequest {
    private $userService;

    public function __construct($userServ) {
        $this->userService = $userServ;
    }

    public function createOrUpdateUser($data) {
        $values = $this->getDataFields($data);

        $user = $this->userService->getUserByPrinciple($values["CERTIFICATE_DN"]);
        if ($user != null) {
            // User already exists, so update it
            $user = $this->userService->editUser($user, $values, $user);
        } else {
            // User doesn't exist, so create one
            $user = $this->userService->register($values);
        }

        $reply = null;
        if ($user != null) {
            $reply["UserId"] = $user->getCertificateDn();
        }

        return $reply;
    }

    public function deleteUser($data) {
        $values = $this->getDataFields($data, false);

        $user = $this->userService->getUserByPrinciple($values["CERTIFICATE_DN"]);
        if ($user == null) {
            throw new \Exception("no user exists with the provided email address");
        }

        $this->userService->deleteUser($user, $user);
        return null;
    }

    private function verifyData($data) {
        // The data must at least contain an email address
        if (!array_key_exists("Email", $data) || $data["Email"] == "") {
            throw new \Exception("no email address provided");
        }
    }

    private function getDataFields($data, $verifyAll = true) {
        $this->verifyData($data);

        if ($verifyAll) {
            if ($data["FirstName"] == "") {
                throw new \Exception("no first name provided");
            }

            if ($data["LastName"] == "") {
                throw new \Exception("no last name provided");
            }
        }

        $title = $data["Title"];
        if ($title == "") {
            $title = "Mr";
        }

        $values["TITLE"] = $title;
        $values["FORENAME"] = $data["FirstName"];
        $values["SURNAME"] = $data["LastName"];
        $values["EMAIL"] = $data["Email"];
        $values["TELEPHONE"] = $data["PhoneNumber"];
        $values["CERTIFICATE_DN"] = $data["Email"];
        return $values;
    }
}
