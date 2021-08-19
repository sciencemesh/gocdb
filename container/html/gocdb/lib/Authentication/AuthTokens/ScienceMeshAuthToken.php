<?php
namespace org\gocdb\security\authentication;
require_once __DIR__.'/../IAuthentication.php';
require_once __DIR__.'/../Exceptions/AuthenticationException.php';

/**
* ScienceMesh specific authentication token.
*/
class ScienceMeshAuthToken implements IAuthentication {
    private $userDetails;
    private $username;
    private $token;
    private $authorities;
    private $initialUsername;

    public function __construct($username, $token) {
        $this->username = $username;
        $this->initialUsername = $username;
        $this->token = $token;
    }

    public static function isStateless() {
        return false;
    }

    public static function isPreAuthenticating() {
        return false;
    }

    public function getCredentials() {
        return "";
    }

    public function getDetails() {
        return $this->userDetails;
    }

    public function getPrinciple() {
        return $this->username;
    }
    public function setPrinciple($username){
        $this->username = $username;
    }

    public function setDetails($userDetails) {
        $this->userDetails = $userDetails;
    }

    public function eraseCredentials() {
    }

    public function getAuthorities(){
        return $this->authorities;
    }

    public function setAuthorities($authorities) {
        $this->authorities = $authorities;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function validate(){
        if($this->getPrinciple() != $this->initialUsername){
            throw new AuthenticationException(null, 'Invalid state, principle does not equal initial username');
        }

        // Query the site accounts service to verify the current user token
        $url = getenv('SITEACC_API') . '/verify-user-token?token=' . urlencode($this->token) . "&user=" . urlencode($this->username) . "&scope=gocdb";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resp = json_decode($data, TRUE);
        if (intval($code) == 200) {
            // Update the current token with the new one that is provided in the response
            $this->setToken($resp["data"]);
        } else {
            throw new AuthenticationException(null, 'The stored user token is invalid: ' . $resp["error"]);
        }
    }
}

?>
