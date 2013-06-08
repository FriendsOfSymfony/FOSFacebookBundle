<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Templating\Helper;

use FOS\FacebookBundle\Templating\Helper\FacebookHelper;

class FacebookHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FOS\FacebookBundle\Templating\Helper\FacebookHelper::initialize
     */
    public function testInitialize()
    {
        $expected = new \stdClass();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating
            ->expects($this->once())
            ->method('render')
            ->with('FOSFacebookBundle::initialize.html.php', array(
                'appId'   => 123,
                'async'   => true,
                'cookie'  => false,
                'culture' => 'en_US',
                'fbAsyncInit' => '',
                'logging' => true,
                'oauth' => true,
                'status'  => false,
                'xfbml'   => false,
            ))
            ->will($this->returnValue($expected));

        $facebookMock = $this->getMockBuilder('FOS\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getAppId'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getAppId')
            ->will($this->returnValue('123'));

        $helper = new FacebookHelper($templating, $facebookMock);
        $this->assertSame($expected, $helper->initialize(array('cookie' => false)));
    }

    /**
     * @covers FOS\FacebookBundle\Templating\Helper\FacebookHelper::loginButton
     */
    public function testLoginButton()
    {
        $expected = new \stdClass();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating
            ->expects($this->once())
            ->method('render')
            ->with('FOSFacebookBundle::loginButton.html.php', array(
                'autologoutlink'  => 'false',
                'label'           => 'testLabel',
                'showFaces'       => 'false',
                'width'           => '',
                'maxRows'         => '1',
                'scope'           => '1,2,3',
                'registrationUrl' => '',
                'size'            => 'medium',
                'onlogin'         => ''
            ))
            ->will($this->returnValue($expected));

        $facebookMock = $this->getMockBuilder('FOS\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getAppId'))
            ->getMock();
        $facebookMock->expects($this->any())
            ->method('getAppId');

        $helper = new FacebookHelper($templating, $facebookMock, true, 'en_US', array(1,2,3) );
        $this->assertSame($expected, $helper->loginButton(array('label' => 'testLabel')));
    }
}
