<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle;
use FOS\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FOSFacebookBundle extends Bundle
{
  
  public function build( ContainerBuilder $container )
  {
    parent::build( $container );
    
    $extension = $container->getExtension( 'security' );
    $extension->addSecurityListenerFactory( new FacebookFactory( ) );
  }
}
