<?php

namespace Bundle\FOS\FacebookBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\DelegatingEngine;

class FacebookHelper extends Helper
{
    protected $templating;
    protected $appId;
    protected $cookie;
    protected $logging;
    protected $culture;
    protected $permissions;

    public function __construct(DelegatingEngine $templating, $appId, $cookie = false, $logging = true, $culture = 'en_US', array $permissions = array())
    {
        $this->templating  = $templating;
        $this->appId       = $appId;
        $this->cookie      = $cookie;
        $this->logging     = $logging;
        $this->culture     = $culture;
        $this->permissions = $permissions;
    }

    /**
     * Returns the HTML necessary for initializing the JavaScript SDK.
     *
     * The default template includes the following parameters:
     *
     *  * appId
     *  * xfbml
     *  * session
     *  * status
     *  * cookie
     *  * logging
     *  * culture
     *
     * @param array  $parameters An array of parameters for the initialization template
     * @param string $name       A template name
     *
     * @return string An HTML string
     */
    public function initialize($parameters = array(), $name = null)
    {
        $name = $name ?: 'FOSFacebookBundle::initialize.php.html';
        return $this->templating->render($name, $parameters + array(
            'fbAsyncInit' => '',
            'appId'       => $this->appId,
            'xfbml'       => false,
            'session'     => null,
            'status'      => false,
            'cookie'      => $this->cookie,
            'logging'     => $this->logging,
            'culture'     => $this->culture,
        ));
    }

    /**
     * Returns the HTML necessary for initializing the JavaScript SDK. It does
     * a synchronous request to Facebook to get the needed HTML.
     *
     * The default template includes the following parameters:
     *
     *  * appId
     *  * xfbml
     *  * session
     *  * status
     *  * cookie
     *  * logging
     *  * culture
     *
     * @param array  $parameters An array of parameters for the initialization template
     * @param string $name       A template name
     *
     * @return string An HTML string
     */
    public function initializeSynchronously($params = array(), $name = null)
    {
        return $this->initialize($params, 'FOSFacebookBundle::initializeSync.php.html');
    }

    public function loginButton($parameters = array(), $name = null)
    {
        $name = $name ?: 'FOSFacebookBundle::loginButton.php.html';
        return $this->templating->render($name, $parameters + array(
            'autologoutlink' => 'false',
            'label'          => '',
            'permissions'    => implode(', ', $this->permissions),
        ));
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'facebook';
    }
}
