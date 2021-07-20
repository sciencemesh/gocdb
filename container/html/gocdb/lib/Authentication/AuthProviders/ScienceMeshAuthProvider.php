<?php
namespace org\gocdb\security\authentication;
include_once __DIR__.'/../_autoload.php';

/**
 * ScienceMesh Authentication provider.
 */
class ScienceMeshAuthProvider implements IAuthenticationProvider {
    private $userDetailsService;

    function __construct(IUserDetailsService $userDetailsService) {
       $this->userDetailsService = $userDetailsService;
    }

    public function authenticate(IAuthentication $auth){
        if($auth == null){
            throw new BadCredentialsException(null, 'Bad credentials - null given');
        }
        if(!$this->supports($auth)){
            throw new BadCredentialsException(null, 'Bad credentials - unsupported token type');
        }

        return $this->authenticateAgainstDB($auth);
    }

    private function authenticateAgainstDB($auth){
        try {
            $username = $auth->getPrinciple();
            $userDetails = $this->userDetailsService->loadUserByUsername($username);
        } catch(UsernameNotFoundException $ex){
            throw new AuthenticationException($ex, 'Username not found');
        }

        if($userDetails->getUsername() == $auth->getPrinciple()){
           $auth->setDetails($userDetails->getGOCDBCustomVal());
           $auth->setAuthorities($userDetails->getAuthorities());
           return $auth;
        }

        throw new AuthenticationException(null, 'Authentication failed');
    }

    public function supports(IAuthentication $auth){
        // Only accept ScientMesh tokens
        if ($auth instanceof ScienceMeshAuthToken) {
            return true
        }

        return false;
    }
}

?>
