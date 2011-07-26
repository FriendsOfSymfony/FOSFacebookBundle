<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * FacebookAuthenticationEntryPoint starts an authentication via Facebook.
 *
 * @author Thomas Adam <thomas.adam@tebot.de>
 */
class FacebookAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected $facebook;
    protected $options;
    protected $permissions;

    /**
     * Constructor
     *
     * @param BaseFacebook $facebook
     * @param array    $options
     */
    public function __construct(\BaseFacebook $facebook, array $options = array(), array $permissions = array())
    {
        $this->facebook = $facebook;
        $this->permissions = $permissions;
        $this->options = new ParameterBag($options);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $response = new RedirectResponse($this->facebook->getLoginUrl(
           array(
                'cancel_url' => $request->getUriForPath($this->options->get('cancel_url', '')),
                'canvas' => $this->options->get('canvas', 0),
                'display' => $this->options->get('display', 'page'),
                'fbconnect' => $this->options->get('fbconnect', 1),
                'req_perms' => implode(',', $this->permissions),
                'redirect_uri' => $request->getUriForPath($this->options->get('check_path', '')),
            ))
        );

        return $response;
    }
}
