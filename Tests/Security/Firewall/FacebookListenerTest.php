<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Security\Firewall\FacebookListener;

use FOS\FacebookBundle\Security\Firewall\FacebookListener;

class FacebookListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FOS\FacebookBundle\Security\Firewall\FacebookListener::attemptAuthentication
     */
    public function testThatCanAttemptAuthenticationWithFacebook()
    {
        $listener = new FacebookListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            $this->getAuthenticationManager(),
            $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface'),
            $this->getHttpUtils(),
            'providerKey',
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface'),
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface')
        );
        $listener->handle($this->getResponseEvent());
    }

    /**
     * @return Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    private function getAuthenticationManager()
    {
        $authenticationManagerMock = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManagerMock->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken'));

        return $authenticationManagerMock;
    }

    /**
     * @return Symfony\Component\Security\Http\HttpUtils
     */
    private function getHttpUtils()
    {
        $httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $httpUtils->expects($this->once())
            ->method('checkRequestPath')
            ->will($this->returnValue(true));

        return $httpUtils;
    }

    /**
     * @return Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getResponseEvent()
    {
        $responseEventMock = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array('getRequest'), array(), '', false);
        $responseEventMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getRequest()));

        return $responseEventMock;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    private function getRequest()
    {
        $requestMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalClone()
            ->getMock();
        $requestMock->expects($this->any())
            ->method('hasSession')
            ->will($this->returnValue('true'));
        $requestMock->expects($this->any())
            ->method('hasPreviousSession')
            ->will($this->returnValue('true'));

        return $requestMock;
    }
}
