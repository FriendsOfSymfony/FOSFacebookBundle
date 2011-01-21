<?php

namespace Bundle\FOS\FacebookBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * FbMetatagsHelper is a helper that manages fb:* tags.
 *
 * Usage:
 *
 * <code>
 *   $view['fbmetatags']->add('admins', 'USER_ID');
 *   $view['fbmetatags']->add('app_id', 'APP_ID');
 *   echo $view['fbmetatags'];
 * </code>
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
class FbMetatagsHelper extends MetatagsHelper
{
    public function __construct($appId)
    {
        $this->add('app_id', $appId);
    }


    /**
     * Returns a tag namespace
     *
     * @return string namespace
     */
    public function getNamespace()
    {
        return 'fb';
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'fbmetatags';
    }
}