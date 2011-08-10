<?php

namespace FOS\FacebookBundle\Security\Logout;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

/**
 * Handler for clearing facebook session
 */
class FacebookLogoutHandler implements LogoutHandlerInterface
{
    /**
     * The facebook instance
     *
     * @var \Facebook
     */
    protected $facebook;

    public function __construct(\Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Invalidate the current facebook session
     * This removes the 'fbs_appid' cookie
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     * @return void
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->facebook->setSession(null);
    }
}
