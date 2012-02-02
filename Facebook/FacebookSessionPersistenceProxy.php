<?php

namespace FOS\FacebookBundle\Facebook;

use Symfony\Component\HttpFoundation\Session;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class FacebookSessionPersistenceProxy implements FacebookInterface
{
    /**
     * @var Symfony\Component\HttpFoundation\Session
     */
    protected $session = null;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var FOS\FacebookBundle\Facebook\FacebookSessionPersistence
     */
    private $sessionPersistence = null;

   /**
    * @param array $config the application configuration.
    * @param Symfony\Component\HttpFoundation\Session
    * @param string
    */
    public function __construct($config, Session $session, $prefix = null)
    {
        $this->config  = $config;
        $this->session = $session;
        $this->prefix  = $prefix;
    }

    public function getUser()
    {
        return $this->getSessionPersistence()->getUser();
    }

    public function getAppId()
    {
        return $this->getSessionPersistence()->getAppId();
    }

    public function setAppId($appId)
    {
        return $this->getSessionPersistence()->setAppId($appId);
    }

    public function getAppSecret()
    {
        return $this->getSessionPersistence()->getAppSecret();
    }

    public function setAppSecret($secret)
    {
        return $this->getSessionPersistence()->setAppSecret($secret);
    }

    public function getApiSecret()
    {
        return $this->getSessionPersistence()->getApiSecret();
    }

    public function setApiSecret($secret)
    {
        return $this->getSessionPersistence()->setApiSecret($secret);
    }

    public function setFileUploadSupport($supported)
    {
        return $this->getSessionPersistence()->setFileUploadSupport($supported);
    }

    public function getFileUploadSupport()
    {
        return $this->getSessionPersistence()->getFileUploadSupport();
    }

    public function useFileUploadSupport()
    {
        return $this->getSessionPersistence()->useFileUploadSupport();
    }

    public function getAccessToken()
    {
        return $this->getSessionPersistence()->getAccessToken();
    }

    public function setAccessToken($token)
    {
        return $this->getSessionPersistence()->setAccessToken($token);
    }

    public function getSignedRequest()
    {
        return $this->getSessionPersistence()->getSignedRequest();
    }

    public function getLoginUrl($params = array())
    {
        return $this->getSessionPersistence()->getLoginUrl($params);
    }

    public function getLogoutUrl($params = array())
    {
        return $this->getSessionPersistence()->getLogoutUrl($params);
    }

    public function getLoginStatusUrl($params = array())
    {
        return $this->getSessionPersistence()->getLoginStatusUrl($params);
    }

    public function api()
    {
        $args = func_get_args();

        return call_user_func_array(array($this->getSessionPersistence(), 'api'), $args);
    }

    public function destroySession()
    {
        return $this->getSessionPersistence()->destroySession();
    }

    public function setSessionPersistence(FacebookInterface $sessionPersistence)
    {
        $this->sessionPersistence = $sessionPersistence;
    }

    /**
     * @return FOS\FacebookBundle\Facebook\FacebookSessionPersistence
     */
    protected function getSessionPersistence()
    {
        if (!$this->sessionPersistence) {
            $this->sessionPersistence = $this->createSessionPersistence();
        }

        return $this->sessionPersistence;
    }

    /**
     * @return FOS\FacebookBundle\Facebook\FacebookSessionPersistence
     */
    protected function createSessionPersistence()
    {
        return new FacebookSessionPersistence($this->config, $this->session, $this->prefix);
    }
}
