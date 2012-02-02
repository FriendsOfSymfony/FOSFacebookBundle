<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Facebook;

use FOS\FacebookBundle\Facebook\FacebookSessionPersistenceProxy;

class FacebookSessionPersistenceProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testThatCanSetAppId()
    {
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $this->assertEquals('234', $facebook->getAppId());
        $facebook->setAppId('345');
    }

    public function testThatCanMakeApiQuery()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('api')
            ->with($this->equalTo('aa'), $this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('api call'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('api call', $facebookProxy->api('aa', 1, 2));
    }

    public function testThatCanSetApiSecret()
    {
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $this->assertEquals('secret', $facebook->getApiSecret());
        $facebook->setApiSecret('secret1');
        $this->assertEquals('secret1', $facebook->getApiSecret());
    }

    public function testThatCanSetAppSecret()
    {
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook->setAppSecret('secret2');
        $this->assertEquals('secret2', $facebook->getAppSecret());
    }

    public function testThatCanSetFileUploadSupport()
    {
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook->setFileUploadSupport(true);
        $this->assertEquals(true, $facebook->getFileUploadSupport());
    }

    public function testThatCanCheckIfUseFileUploadSupport()
    {
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook->setFileUploadSupport(true);
        $this->assertEquals(true, $facebook->useFileUploadSupport());
    }

    public function testThatCanSetAccessToken()
    {
        $session = $this->getSession();
        $session->expects($this->any())
            ->method('all')
            ->will($this->returnValue(array('1', '_fos_facebook_123' => 'test')));
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $this->assertEquals('234|secret', $facebook->getAccessToken());
        $facebook->setAccessToken('token1');
        $this->assertEquals('token1', $facebook->getAccessToken());
    }

    public function testThatCanGetSignedRequest()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('getSignedRequest')
            ->will($this->returnValue('signed request'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('signed request', $facebookProxy->getSignedRequest());
    }

    public function testThatCanGetLogoutUrl()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('getLogoutUrl')
            ->with($this->equalTo(array('test' => '123')))
            ->will($this->returnValue('http://logout'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('http://logout', $facebookProxy->getLogoutUrl(array('test' => '123')));
    }

    public function testThatCanGetLoginUrl()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('getLoginUrl')
            ->with($this->equalTo(array('test' => '123')))
            ->will($this->returnValue('http://login'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('http://login', $facebookProxy->getLoginUrl(array('test' => '123')));
    }

    public function testThatCanGetUser()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('user from facebook'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('user from facebook', $facebookProxy->getUser());
    }

    public function testThatCanDestroySession()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('destroySession');

        $facebookProxy->setSessionPersistence($facebook);

        $facebookProxy->destroySession();
    }

    public function testThatCanGetLoginStatusUrl()
    {
        $facebookProxy = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook = $this->getMock('FOS\FacebookBundle\Facebook\FacebookInterface');
        $facebook->expects($this->once())
            ->method('getLoginStatusUrl')
            ->with($this->equalTo(array('test' => '123')))
            ->will($this->returnValue('http://login'));

        $facebookProxy->setSessionPersistence($facebook);

        $this->assertEquals('http://login', $facebookProxy->getLoginStatusUrl(array('test' => '123')));
    }

    public function testThatCanGetAccessToken()
    {
        $session = $this->getSession();
        $session->expects($this->any())
            ->method('all')
            ->will($this->returnValue(array()));
        $session->expects($this->any())
            ->method('has')
            ->will($this->returnValue(array(true)));
        $session->expects($this->any())
            ->method('get')
            ->will($this->returnValue('state'));
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $_REQUEST['code'] = '123';
        $_REQUEST['state'] = 'state';
        $_SERVER['HTTP_HOST'] = 'http://localhost';
        $_SERVER['REQUEST_URI'] = 'index.php';

        $this->assertEquals('234|secret', $facebook->getAccessToken());
    }

    private function getSession()
    {
        return $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
