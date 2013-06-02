<?php

namespace FOS\FacebookBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
  
  public function writeAuthInSessionAction( )
  {
    $accessToken = $this->getRequest( )->get( "accessToken" );
    $this->get( "session" )->set( "fb.accessToken", $accessToken );
    return new JsonResponse( array( "accessToken" => $accessToken ));
  }
}
