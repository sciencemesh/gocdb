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

const FIELD_EMAIL = "Email";
const FIELD_TITLE = "Title";
const FIELD_FIRSTNAME = "FirstName";
const FIELD_LASTNAME = "LastName";
const FIELD_PHONENUMBER = "PhoneNumber";

const REPLY_USERID = "UserId";
const REPLY_STATUS = "Status";

class UserRequest {
    private $userService;

    public function __construct($userServ) {
        $this->userService = $userServ;
    }

    public function createOrUpdateUser($data) {
        $values = $this->getGOCDBDataFields($data);
        $status = "Unknown";

        $user = $this->userService->getUserByPrinciple($values["CERTIFICATE_DN"]);
        if ($user != null) {
            // User already exists, so update it
            $user = $this->userService->editUser($user, $values, $user);
            $status = "Modified";
        } else {
            // User doesn't exist, so create one
            $user = $this->userService->register($values);
            $status = "Created";
        }

        $reply = null;
        if ($user != null) {
            $reply[REPLY_USERID] = $user->getCertificateDn();
            $reply[REPLY_STATUS] = $status;
        }
        return $reply;
    }

    public function deleteUser($data) {
        $values = $this->getGOCDBDataFields($data, false);

        $user = $this->userService->getUserByPrinciple($values["CERTIFICATE_DN"]);
        if ($user == null) {
            throw new \Exception("no user exists with the provided email address");
        }

        $this->userService->deleteUser($user, $user);

        $reply[REPLY_USERID] = $user->getCertificateDn();
        $reply[REPLY_STATUS] = "Deleted";
        return $reply;
    }

    private function getGOCDBDataFields($data, $verifyAll = true) {
        // The data must at least contain an email address
        if (!array_key_exists(FIELD_EMAIL, $data) || $data[FIELD_EMAIL] == "") {
            throw new \Exception("no email address provided");
        }

        if ($verifyAll) {
            if ($data[FIELD_FIRSTNAME] == "") {
                throw new \Exception("no first name provided");
            }

            if ($data[FIELD_LASTNAME] == "") {
                throw new \Exception("no last name provided");
            }
        }

        $title = $data[FIELD_TITLE];
        if ($title == "") {
            $title = "Mr";
        }

        $values["TITLE"] = $title;
        $values["FORENAME"] = $data[FIELD_FIRSTNAME];
        $values["SURNAME"] = $data[FIELD_LASTNAME];
        $values["EMAIL"] = $data[FIELD_EMAIL];
        $values["TELEPHONE"] = $data[FIELD_PHONENUMBER];
        $values["CERTIFICATE_DN"] = $data[FIELD_EMAIL];
        return $values;
    }
}
