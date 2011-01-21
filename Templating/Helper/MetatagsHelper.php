<?php

namespace Bundle\FOS\FacebookBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * MetatagsHelper is base class for FbMetatagsHelper and OgMetatagsHelper
 *
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
abstract class MetatagsHelper extends Helper
{
    protected $tags = array();

    
    abstract public function getNamespace();
    

    /**
     * Adds a metatag
     *
     * @param string $property Property
     * @param string $content Content
     */
    public function add($property, $content)
    {
        $this->tags[$property] = $content;
    }

    /**
     * Returns all tags.
     *
     * @return array An array of tags
     */
    public function get()
    {
        return $this->tags;
    }
    
     
    /**
     * Returns HTML representation of the meta tags.
     *
     * @return string The HTML representation of the meta tags.
     */
    public function render()
    {
        $html = '';
        foreach ($this->tags as $property => $content) {
            $html .= sprintf('<meta property="%s" content="%s" />', $this->getNamespace().':'.$property, htmlspecialchars($content, ENT_QUOTES, $this->charset))."\n";
        }

        return $html;
    }
    

    /**
     * Outputs HTML representation of the metatags.
     *
     */
    public function output()
    {
        echo $this->render();
    }

    /**
     * Returns a string representation of this helper as HTML.
     *
     * @return string The HTML representation of meta tags.
     */
    public function __toString()
    {
        return $this->render();
    }
    
}
