<?php

namespace Bundle\FOS\FacebookBundle\Twig\TokenParser;



use Bundle\FOS\FacebookBundle\Twig\Node\FbMetatagsNode;

class FbMetatagsTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A \Twig_Token instance
     *
     * @return \Twig_NodeInterface A \Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new FbMetatagsNode($token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return 'fbmetatags';
    }
}
