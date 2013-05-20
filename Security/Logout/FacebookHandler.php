<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Security\Logout;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

/**
 * Listener for the logout action
 *
 * This handler will clear the application's Facebook cookie.
 */
class FacebookHandler implements LogoutHandlerInterface
{
  private $facebook;
  
  public function __construct( \BaseFacebook $facebook )
  {
    $this->facebook = $facebook;
  }
  
  public function logout( Request $request, Response $response, TokenInterface $token )
  {
    $this->facebook->destroySession( );
  }
}
