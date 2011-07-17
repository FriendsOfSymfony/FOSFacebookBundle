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
    protected $permissions;
    protected $facebook;

    public function __construct(EngineInterface $templating, \BaseFacebook $facebook, $logging = true, $culture = 'en_US', array $permissions = array())
    {
        $this->templating  = $templating;
        $this->logging     = $logging;
        $this->culture     = $culture;
        $this->permissions = $permissions;
        $this->facebook    = $facebook;
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
        $name = $name ?: 'FOSFacebookBundle::initialize.html.php';
        return $this->templating->render($name, $parameters + array(
            'async'       => true,
            'fbAsyncInit' => '',
            'appId'       => (string) $this->facebook->getAppId(),
            'xfbml'       => false,
            'session'     => true,
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
            'autologoutlink' => 'false',
            'label'          => '',
            'permissions'    => implode(',', $this->permissions),
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
