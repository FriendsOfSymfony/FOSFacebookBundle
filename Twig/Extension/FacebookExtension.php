<?php

namespace Bundle\FOS\FacebookBundle\Twig\Extension;

use Bundle\FOS\FacebookBundle\Twig\TokenParser\FacebookTokenParser;

/**
 *
 */
class FacebookExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct($helper)
    {
        $this->helper = $helper;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'facebook_initialize' => new \Twig_Function_Method($this, 'renderInitialize', array('is_safe' => array('html'))),
            'facebook_initialize_sync' => new \Twig_Function_Method($this, 'renderInitializeSynchronously', array('is_safe' => array('html'))),
            'facebook_login_button' => new \Twig_Function_Method($this, 'renderLoginButton', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'facebook';
    }

    public function renderInitialize($parameters = array(), $name = null)
    {
        return $this->helper->initialize($parameters, $name);
    }

    public function renderInitializeSynchronously($parameters = array(), $name = null)
    {
        return $this->helper->initializeSynchronously($parameters, $name);
    }

    public function renderLoginButton($parameters = array(), $name = null)
    {
        return $this->helper->loginButton($parameters, $name);
    }
}
