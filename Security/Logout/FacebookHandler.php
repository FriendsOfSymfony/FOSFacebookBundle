<?php
namespace FOS\FacebookBundle\Security\Logout;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Listener for the logout action
 * 
 * This handler will erase the cookie keeped by facebook
 * for the application.
 * 
 */
class FacebookHandler implements LogoutHandlerInterface
{
    
    /**
     * @var \Facebook $facebookApi
     */
    private $facebookApi;
    
    /**
     * __construct
     * 
     * @param \Facebook $facebookApi
     */
    public function __construct(\BaseFacebook $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }
    
    /**
     * This method is called by the LogoutListener when a user has requested
     * to be logged out. Usually, you would unset session variables, or remove
     * cookies, etc.
     *
     * @param Request        $request
     * @param Response       $response
     * @param TokenInterface $token
     * @return void
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $fb_cookie = sprintf(
            'fbsr_%d',
            $this->facebookApi->getAppId()
        );
        $response->headers->clearCookie($fb_cookie);
    }
    
}
