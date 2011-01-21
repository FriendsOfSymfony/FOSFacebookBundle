<?php

namespace Bundle\FOS\FacebookBundle\Tests\Templating\Helper;

use Bundle\FOS\FacebookBundle\Templating\Helper\OgMetatagsHelper;

class OgMetatagsHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function defaultTag()
    {
        $helper = new OgMetatagsHelper();
        $this->assertSame(array(), $helper->get());
    }

    /**
     * @test
     */
    public function render()
    {
        $expected = '<meta property="og:title" content="The Rock" />'.PHP_EOL.'<meta property="og:type" content="move" />'.PHP_EOL;

        $helper = new OgMetatagsHelper();
        $helper->add('title', 'The Rock');
        $helper->add('type', 'move');

        $this->assertSame($expected, $helper->render());
    }
}
