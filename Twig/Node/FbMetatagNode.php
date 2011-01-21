<?php

namespace  Bundle\FOS\FacebookBundle\Twig\Node;



class FbMetatagNode extends \Twig_Node
{
    public function __construct(\Twig_Node_Expression $property, \Twig_Node_Expression $content, $lineno, $tag = null)
    {
        parent::__construct(array('property' => $property, 'content' => $content), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo \$this->env->getExtension('templating')->getContainer()->get('fos_facebook.helper.fbmetatags')->add(")
            ->subcompile($this->getNode('property'))
            ->raw(', ')
            ->subcompile($this->getNode('content'))
            ->raw(");\n")
        ;
    }
}
