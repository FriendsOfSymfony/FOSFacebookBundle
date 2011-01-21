<?php

namespace FOS\FacebookBundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Authentication\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Exception\AuthenticationException;
use Symfony\Component\Security\SecurityContext;

/**
 * FacebookAuthenticationEntryPoint starts an authentication via Facebook.
 *
 * @author Thomas Adam <thomas.adam@tebot.de>
 */
class FacebookAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected $facebook;
    protected $options;

    /**
     * Constructor
     *
     * @param Facebook $facebook 
     * @param array    $options
     */
    public function __construct(\Facebook $facebook, array $options = array())
    {
        $this->facebook = $facebook;
        $this->options = new ParameterBag($options);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $response = new Response();
        $response->setRedirect($this->facebook->getLoginUrl(array(
                    'cancel_url' => $this->options->get('cancel_url', $request->getUri()),
                    'canvas' => $this->options->get('canvas', 0),
                    'display' => $this->options->get('display', 'page'),
                    'fbconnect' => $this->options->get('fbconnect', 1),
                    'req_perms' => $this->options->get('req_perms', ''),
                    'next' => $this->options->get('next', $request->getUri()),
                ))
        );

        return $response;
    }
}
