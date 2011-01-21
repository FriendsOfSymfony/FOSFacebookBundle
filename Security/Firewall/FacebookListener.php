<?php

namespace FOS\FacebookBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Security\Firewall\PreAuthenticatedListener;

/**
 * Facebook authentication listener.
 */
class FacebookListener extends PreAuthenticatedListener
{
    protected function getPreAuthenticatedData(Request $request)
    {
        // array(id_user, query_string)
        return array('_fos_facebook_', '_no_pass_');
    }
}