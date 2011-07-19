<?php

namespace FOS\FacebookBundle\Facebook;

use Symfony\Component\HttpFoundation\Session;

/**
 * Implements Symfony2 session persistence for Facebook.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FacebookSessionPersistence extends \BaseFacebook
{
    const PREFIX = '_fos_facebook_';

    private $session;
    private $prefix;

    public function __construct($config, Session $session, $prefix = self::PREFIX)
    {
        $this->session = $session;
        $this->prefix  = $prefix;

        parent::__construct($config);
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
        $this->session->set($this->prefix.$key, $value);
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
        return $this->session->get($this->prefix.$key, $default);
    }

    /**
     * Clear the data with $key from the persistent storage
     *
     * @param string $key
     * @return void
     */
    protected function clearPersistentData($key)
    {
        $this->session->remove($this->prefix.$key);
    }

    /**
     * Clear all data from the persistent storage
     *
     * @return void
     */
    protected function clearAllPersistentData()
    {
        foreach ($this->session->getAttributes() as $k => $v) {
            if (0 !== strpos($k, $this->prefix)) {
                continue;
            }

            $this->session->remove($k);
        }
    }
}