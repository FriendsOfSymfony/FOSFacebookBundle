<?php

namespace FOS\FacebookBundle\Tests\DependencyInjection;

use FOS\FacebookBundle\DependencyInjection\FacebookExtension;

class FacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension::apiLoad
     */
    public function testApiLoadLoadsDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('fos_facebook.api')
            ->will($this->returnValue(false));

        $extension = $this->getMockBuilder('FOS\\FacebookBundle\\DependencyInjection\\FacebookExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->once())
            ->method('loadDefaults')
            ->with($container);

        $extension->apiLoad(array(), $container);
    }

    /**
     * @covers Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension::apiLoad
     */
    public function testApiLoadDoesNotReloadDefaults()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('fos_facebook.api')
            ->will($this->returnValue(true));

        $extension = $this->getMockBuilder('FOS\\FacebookBundle\\DependencyInjection\\FacebookExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->never())
            ->method('loadDefaults');

        $extension->apiLoad(array(), $container);
    }

    /**
     * @covers Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension::apiLoad
     */
    public function testApiLoadSetsAlias()
    {
        $alias = 'foo';

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('fos_facebook.api')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('setAlias')
            ->with($alias, 'fos_facebook.api');

        $extension = new FacebookExtension();
        $extension->apiLoad(array('alias' => $alias), $container);
    }

    /**
     * @covers Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension::apiLoad
     * @dataProvider parameterNames
     */
    public function testApiLoadSetParameters($name)
    {
        $value = 'foo';

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('fos_facebook.api')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('fos_facebook.'.$name, $value);

        $extension = new FacebookExtension();
        $extension->apiLoad(array($name => $value), $container);
    }

    public function parameterNames()
    {
        return array(
            array('class'),
            array('app_id'),
            array('secret'),
            array('cookie'),
            array('domain'),
            array('logging'),
            array('culture'),
        );
    }
}
