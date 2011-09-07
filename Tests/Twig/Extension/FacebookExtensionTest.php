<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Twig\Extension;

use FOS\FacebookBundle\Twig\Extension\FacebookExtension;
use FOS\FacebookBundle\Templating\Helper\FacebookHelper;

class FacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FOS\FacebookBundle\Twig\Extension\FacebookExtension::getName
     */
    public function testGetName()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $extension = new FacebookExtension($containerMock);
        $this->assertSame('facebook', $extension->getName());
    }

    /**
     * @covers FOS\FacebookBundle\Twig\Extension\FacebookExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $extension = new FacebookExtension($containerMock);
        $functions = $extension->getFunctions();
        $this->assertInstanceOf('\Twig_Function_Method', $functions['facebook_initialize']);
        $this->assertInstanceOf('\Twig_Function_Method', $functions['facebook_login_button']);
    }

    /**
     * @covers FOS\FacebookBundle\Twig\Extension\FacebookExtension::renderInitialize
     */
    public function testRenderInitialize()
    {
        $helperMock = $this->getMockBuilder('FOS\FacebookBundle\Templating\Helper\FacebookHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('initialize')
            ->will($this->returnValue('returnedValue'));
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->expects($this->once())
            ->method('get')
            ->with('fos_facebook.helper')
            ->will($this->returnValue($helperMock));
 
        $extension = new FacebookExtension($containerMock);
        $this->assertSame('returnedValue', $extension->renderInitialize());
    }
    
    /**
     * @covers FOS\FacebookBundle\Twig\Extension\FacebookExtension::renderloginButton
     */
    public function testRenderLoginButton()
    {
        $helperMock = $this->getMockBuilder('FOS\FacebookBundle\Templating\Helper\FacebookHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('loginButton')
            ->will($this->returnValue('returnedValueLogin'));
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->expects($this->once())
            ->method('get')
            ->with('fos_facebook.helper')
            ->will($this->returnValue($helperMock));
 
        $extension = new FacebookExtension($containerMock);
        $this->assertSame('returnedValueLogin', $extension->renderLoginButton());
    }
}
