<?php

namespace Bundle\FOS\FacebookBundle\Tests\Templating\Helper;

use Bundle\FOS\FacebookBundle\Templating\Helper\FbMetatagsHelper;

class FbMetatagsHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function defaultAppIdTag()
    {
        $helper = new FbMetatagsHelper(123);

        $this->assertSame(array('app_id' => 123), $helper->get());
    }

    /**
     * @test
     */
    public function render()
    {
        $expected = '<meta property="fb:app_id" content="123" />'.PHP_EOL.'<meta property="fb:admins" content="456" />'.PHP_EOL;

        $helper = new FbMetatagsHelper(123);
        $helper->add('admins', 456);


        $this->assertSame($expected, $helper->render());
    }
}
