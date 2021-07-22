<?php
namespace org\gocdb\security\authentication;

/**
 * @see IFirewallComponent
 */
class FirewallComponent implements IFirewallComponent {
    private $authManager;
    private $securityContext;
    private $config;

    function __construct(
            IAuthenticationManager $authManager,
            ISecurityContext $securityContext,
            IConfigFirewallComponent $config) {

        $this->authManager = $authManager;
        $this->securityContext = $securityContext;
        $this->config = $config;
    }

    /**
     * @see ISecurityContext::getAuthentication()
     */
    public function getAuthentication(){
        try {
            $auth = $this->securityContext->getAuthentication();
        } catch (\Exception $e) {
            // The stored token is invalid, so unset the authentication
            $auth = null;
            $this->setAuthentication(null);
        }

        // Check session variables first; this means that the user did a successful login attempt
        if (isset($_SESSION["sm_auth_token"]) && isset($_SESSION["sm_auth_email"])) {
            $this->setAuthentication(null);

            $token = $_SESSION["sm_auth_token"];
            unset($_SESSION["sm_auth_token"]);
            $email = $_SESSION["sm_auth_email"];
            unset($_SESSION["sm_auth_email"]);

            $auth = $this->authenticateUserToken($token, $email);
            $this->setAuthentication($auth);
        } else if ($auth == null) {
            if (isset($_SESSION["sm_ext_token"]) && isset($_SESSION["sm_ext_email"])) {
                // Token was already authenticated
                $auth = new ScienceMeshAuthToken($_SESSION["sm_ext_email"], $_SESSION["sm_ext_token"]);
                $this->setAuthentication($auth);
            }
        }

        if ($auth != null) {
            try {
                $auth->validate();

                // Validation causes a new token, so update the stored one
                $this->storeAuthenticationToken($auth);
            } catch (\Exception $e) {
                // The token is invalid, so unset the authentication
                $auth = null;
                $this->setAuthentication(null);
            }
        }

        return $auth;
    }

    /**
     * @see ISecurityContext::setAuthentication($auth)
     */
    public function setAuthentication($auth = null){
        $this->storeAuthenticationToken($auth);
        return $this->securityContext->setAuthentication($auth);
    }

    private function authenticateUserToken($token, $email) {
        $auth = new ScienceMeshAuthToken($email, $token);
        try {
            $auth = $this->authenticate($auth);
        } catch (\Exception $e) {
            // Errors here are uncritical, so keep the auth token
        }
        return $auth;
    }

    private function storeAuthenticationToken($auth) {
        if ($auth) {
            $_SESSION["sm_ext_token"] = $auth->getToken();
            $_SESSION["sm_ext_email"] = $auth->getPrinciple();
        } else {
            unset($_SESSION["sm_ext_token"]);
            unset($_SESSION["sm_ext_email"]);
        }
    }

    /**
     * @see IAuthenticationManager::authenticate($auth)
     */
    public function authenticate(IAuthentication $auth){
        return $this->authManager->authenticate($auth);
    }

    /**
     * @see IFirewallComponent
     * @throws \LogicException if no providers configured
     */
    public function supports(IAuthentication $auth) {
        $providers = $this->config->getAuthProviders();
        if (empty($providers)) {
            throw new \LogicException("Configuration Error - No AuthenticationProviders are configured");
        }
        foreach ($providers as $provider) {
            if ($provider->supports($auth)) {
                return true;
            }
        }
        return false;
    }
}
