<?php

namespace FOS\FacebookBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FOSFacebookBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }

}
