<?php
namespace org\gocdb\security\authentication;
require_once __DIR__.'/../IAuthentication.php';

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
           throw new AuthenticationException('Invalid state, principle does not equal initial username');
       }

       // TODO: Verify token
    }
}

?>
