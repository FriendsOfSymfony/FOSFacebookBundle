<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class FacebookHelper extends Helper
{
    protected $templating;
    protected $logging;
    protected $culture;
    protected $scope;
    protected $facebook;

    public function __construct(EngineInterface $templating, \BaseFacebook $facebook, $logging = true, $culture = 'en_US', array $scope = array())
    {
        $this->templating  = $templating;
        $this->logging     = $logging;
        $this->culture     = $culture;
        $this->scope       = $scope;
        $this->facebook    = $facebook;
    }

    /**
     * Returns the HTML necessary for initializing the JavaScript SDK.
     *
     * The default template includes the following parameters:
     *
     *  * async
     *  * fbAsyncInit
     *  * appId
     *  * xfbml
     *  * oauth
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
        $name = $name ?: 'FOSFacebookBundle::initialize.html.php';
        return $this->templating->render($name, $parameters + array(
            'async'       => true,
            'fbAsyncInit' => '',
            'appId'       => (string) $this->facebook->getAppId(),
            'xfbml'       => false,
            'oauth'       => true,
            'status'      => false,
            'cookie'      => true,
            'logging'     => $this->logging,
            'culture'     => $this->culture,
        ));
    }

    public function loginButton($parameters = array(), $name = null)
    {
        $name = $name ?: 'FOSFacebookBundle::loginButton.html.php';
        return $this->templating->render($name, $parameters + array(
            'autologoutlink'  => 'false',
            'label'           => '',
            'showFaces'       => 'false',
            'width'           => '',
            'maxRows'         => '1',
            'scope'           => implode(',', $this->scope),
            'registrationUrl' => '',
            'size'            => 'medium',
            'onlogin'         => ''
        ));
    }

    public function logoutUrl($parameters = array(), $name = null)
    {
        return $this->facebook->getLogoutUrl($parameters);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'facebook';
    }
}
