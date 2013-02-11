<?php

namespace FOS\FacebookBundle\Facebook;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

/**
 * Implements Symfony2 session persistence for Facebook.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FacebookSessionPersistence extends \BaseFacebook
{
    const PREFIX = '_fos_facebook_';

    protected $session;
    protected $prefix;
    protected static $kSupportedKeys = array('state', 'code', 'access_token', 'user_id');

    /**
     * @param array $config the application configuration.
     * @see BaseFacebook::__construct in facebook.php
     */
    public function __construct($config, Session $session, $prefix = self::PREFIX)
    {
        $this->session = $session;
        $this->prefix  = $prefix;

        $this->setAppId($config['appId']);
        $this->setAppSecret($config['secret']);
        if (isset($config['fileUpload'])) {
            $this->setFileUploadSupport($config['fileUpload']);
        }
        // Add trustProxy configuration
        $this->trustForwarded = isset($config['trustForwarded']) ? $config['trustForwarded'] : Request::isProxyTrusted();
    }

    public function getLoginUrl($params = array())
    {
        $this->establishCSRFTokenState();
        $currentUrl = $this->getCurrentUrl();

        // if 'scope' is passed as an array, convert to comma separated list
        $scopeParams = isset($params['scope']) ? $params['scope'] : null;
        if ($scopeParams && is_array($scopeParams)) {
            $params['scope'] = implode(',', $scopeParams);
        }

        return $this->getUrl(
            'www',
            'dialog/oauth',
            array_merge(
                array(
                    'client_id' => $this->getAppId(),
                    'redirect_uri' => $currentUrl, // possibly overwritten
                    'state' => $this->getState(),
                ),
                $params
            )
        );
    }

    protected function getCode()
    {
        if (isset($_REQUEST['code'])) {
            if ($this->getState() !== null &&
                isset($_REQUEST['state']) &&
                $this->getState() === $_REQUEST['state']) {

                    // CSRF state has done its job, so clear it
                    $this->setState(null);
                    $this->clearPersistentData('state');

                    return $_REQUEST['code'];
            } else {
                self::errorLog('CSRF state token does not match one provided.');

                return false;
            }
        }

        return false;
    }

    protected function establishCSRFTokenState()
    {
        if ($this->getState() === null) {
            $this->setState(md5(uniqid(mt_rand(), true)));
        }
    }

    /**
     * Stores the given ($key, $value) pair, so that future calls to
     * getPersistentData($key) return $value. This call may be in another request.
     *
     * @param string $key
     * @param array $value
     *
     * @return void
     */
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to setPersistentData.');

            return;
        }

        $this->session->set($this->constructSessionVariableName($key), $value);
    }

    /**
     * Get the data for $key, persisted by BaseFacebook::setPersistentData()
     *
     * @param string $key The key of the data to retrieve
     * @param boolean $default The default value to return if $key is not found
     *
     * @return mixed
     */
    protected function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to getPersistentData.');

            return $default;
        }

        $sessionVariableName = $this->constructSessionVariableName($key);
        if ($this->session->has($sessionVariableName)) {
            return $this->session->get($sessionVariableName);
        }

        return $default;
    }

    /**
     * Clear the data with $key from the persistent storage
     *
     * @param string $key
     * @return void
     */
    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to clearPersistentData.');

            return;
        }

        $this->session->remove($this->constructSessionVariableName($key));
    }

    /**
     * Clear all data from the persistent storage
     *
     * @return void
     */
    protected function clearAllPersistentData()
    {
        foreach ($this->session->all() as $k => $v) {
            if (0 !== strpos($k, $this->prefix)) {
                continue;
            }

            $this->session->remove($k);
        }
    }

    protected function constructSessionVariableName($key)
    {
        return $this->prefix.implode(
            '_',
            array(
                'fb',
                $this->getAppId(),
                $key,
            )
        );
    }

    private function getState()
    {
        return $this->getPersistentData('state', null);
    }

    private function setState($state)
    {
        $this->setPersistentData('state', $state);
    }
}
