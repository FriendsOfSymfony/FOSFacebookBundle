<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Security\Authentication\Provider;

use FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FacebookProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatUserCheckerCannotBeNullWhenUserProviderIsNotNull()
    {
        $facebookProvider = new FacebookProvider('main', $this->getMock('\BaseFacebook'), $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface'));
    }

    /**
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     */
    public function testThatCannotAuthenticateWhenTokenIsNotFacebookUserToken()
    {
        $facebookProvider = new FacebookProvider('main', $this->getMock('\BaseFacebook'));
        $this->assertNull($facebookProvider->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
    }

    /**
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::supports
     */
    public function testThatCannotAuthenticateWhenTokenFromOtherFirewall()
    {
        $providerKeyForProvider = 'main';
        $providerKeyForToken    = 'connect';

        $facebookProvider = new FacebookProvider($providerKeyForProvider, $this->getMock('\BaseFacebook'));

        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getProviderKey'), array($providerKeyForToken));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKeyForToken));

        $this->assertFalse($facebookProvider->supports($tokenMock));
        $this->assertNull($facebookProvider->authenticate($tokenMock));
    }

    /**
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::supports
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatCanAuthenticateUserWithoutUserProvider()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMock('\BaseFacebook', array('getUser'));
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock);

        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getAttributes', 'getProviderKey'), array($providerKey));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $this->assertTrue($facebookProvider->supports($tokenMock));
        $this->assertEquals('123', $facebookProvider->authenticate($tokenMock)->getUser());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenUserProviderThrowsAuthenticationException()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMock('\BaseFacebook', array('getUser'));
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->throwException(new AuthenticationException('test')));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenUserProviderDoesNotReturnUsetInterface()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMock('\BaseFacebook', array('getUser'));
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->returnValue('234'));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenCannotRetrieveFacebookUserFromSession()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMock('\BaseFacebook', array('getUser'));
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue(false));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');

        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers FOS\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatCanAutenticateUsingUserProvider()
    {
        $providerKey = 'main';

        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $userMock->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue('l3l0'));
        $userMock->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array()));

        $facebookMock = $this->getMock('\BaseFacebook', array('getUser'));
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->returnValue($userMock));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userCheckerMock->expects($this->once())
            ->method('checkPostAuth');

        $tokenMock = $this->getMock('FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken', array('getAttributes', 'getProviderKey'), array($providerKey));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $this->assertEquals('l3l0', $facebookProvider->authenticate($tokenMock)->getUsername());
    }
}
