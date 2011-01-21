<?php

namespace Bundle\FOS\FacebookBundle\Templating\Helper;


/**
 * OgMetatagsHelper is a helper that manages og:* tags.
 *
 * Usage:
 *
 * <code>
 *   $view['ogmetatags']->add('title', 'The Rock');
 *   $view['ogmetatags']->add('type', 'movie');
 *   $view['ogmetatags']->add('image', 'http://ia.media-imdb.com/rock.jpg');
 *   $view['ogmetatags']->add('site_name', 'IMDb');
 *   
 *   echo $view['ogmetatags'];
 * </code>
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
class OgMetatagsHelper extends MetatagsHelper
{

    /**
     * Returns a tag namespace 
     *
     * @return string namespace
     */
    public function getNamespace()
    {
        return 'og';
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'ogmetatags';
    }
}
