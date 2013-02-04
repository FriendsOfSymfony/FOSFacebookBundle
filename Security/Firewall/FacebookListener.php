<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Security\Firewall;

use FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

/**
 * Facebook authentication listener.
 */
class FacebookListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        $accessToken = $request->get('access_token');

        return $this->authenticationManager->authenticate(new FacebookUserToken($this->providerKey, '', array(), $accessToken));
    }
}
