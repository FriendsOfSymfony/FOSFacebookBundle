<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\DependencyInjection;

use FOS\FacebookBundle\DependencyInjection\FOSFacebookExtension;

class FOSFacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FOS\FacebookBundle\DependencyInjection\FOSFacebookExtension::load
     */
    public function testLoadFailure()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $extension = $this->getMockBuilder('FOS\\FacebookBundle\\DependencyInjection\\FOSFacebookExtension')
            ->getMock();

        $extension->load(array(array()), $container);
    }

    /**
     * @covers FOS\FacebookBundle\DependencyInjection\FOSFacebookExtension::load
     */
    public function testLoadSetParameters()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag
            ->expects($this->any())
            ->method('add');

        $container
            ->expects($this->any())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        $extension = new FOSFacebookExtension();
        $configs = array(
            array('class' => array('api' => 'foo')),
            array('file' => 'foo'),
            array('app_id' => 'foo'),
            array('secret' => 'foo'),
            array('cookie' => 'foo'),
            array('domain' => 'foo'),
            array('logging' => 'foo'),
            array('culture' => 'foo'),
            array('permissions' => array('email')),
        );
        $extension->load($configs, $container);
    }

    /**
     * @covers FOS\FacebookBundle\DependencyInjection\FOSFacebookExtension::load
     */
    public function testThatCanSetContainerAlias()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->once())
            ->method('setAlias')
            ->with($this->equalTo('facebook_alias'), $this->equalTo('fos_facebook.api'));

        $configs = array(
            array('class' => array('api' => 'foo')),
            array('file' => 'foo'),
            array('app_id' => 'foo'),
            array('secret' => 'foo'),
            array('cookie' => 'foo'),
            array('domain' => 'foo'),
            array('logging' => 'foo'),
            array('culture' => 'foo'),
            array('permissions' => array('email')),
            array('alias' => 'facebook_alias')
        );
        $extension = new FOSFacebookExtension();
        $extension->load($configs, $container);
    }
}
