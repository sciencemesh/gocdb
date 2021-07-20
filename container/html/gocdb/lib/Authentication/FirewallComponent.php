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
    // TODO: Do this properly
    // NOTE: Called by framework as first entry point for any web call for authentication
    public function getAuthentication(){
        $auth = $this->securityContext->getAuthentication();

        // Initiate token creation

        // Check session variables first
        if (isset($_SESSION["auth_username"]) && isset($_SESSION["auth_password"])) {
            $this->setAuthentication(null);

            $username = $_SESSION["auth_username"];
            unset($_SESSION["auth_username"]);
            $password = $_SESSION["auth_password"];
            unset($_SESSION["auth_password"]);

            $auth = $this->authenticateUsernamePassword($username, $password);

            if ($auth) {
                $this->setAuthentication($auth);
            }
        } else if ($auth == null) {
            if (isset($_SESSION["ext_username"]) && isset($_SESSION["ext_password"])) {
                // Token was already authenticated
                $auth = new UsernamePasswordAuthenticationToken($_SESSION["ext_username"], $_SESSION["ext_password"]);
                $this->setAuthentication($auth);
            }
        }

        return $auth;
    }

    /**
     * @see ISecurityContext::setAuthentication($auth)
     */
    // TODO: Do this properly
    public function setAuthentication($auth = null){
        if ($auth) {
            $_SESSION["ext_username"] = $auth->getPrinciple();
            $_SESSION["ext_password"] = $auth->getCredentials();
        } else {
            unset($_SESSION["ext_username"]);
            unset($_SESSION["ext_password"]);
        }

        return $this->securityContext->setAuthentication($auth);
    }

    // TODO: Do this properly
    private function authenticateUsernamePassword($username, $password) {
        $auth = new UsernamePasswordAuthenticationToken($username, $password);

        // TODO: Totally advanced security
        if ($auth->getPrinciple() == $auth->getCredentials()) {
            try {
                $auth = $this->authenticate($auth);
            } catch (\Exception $e) {
            }
        } else {
            $auth = null;
        }

        return $auth;
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
            throw new \LogicException("Configuration Error - "
            . "No AuthenticationProviders are configured");
        }
        foreach ($providers as $provider) {
            if ($provider->supports($auth)) {
                return true;
            }
        }
        return false;
    }
}
