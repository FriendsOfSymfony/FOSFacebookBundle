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
use FOS\FacebookBundle\Facebook\FacebookSessionPersistence;

class FacebookSessionPersistenceProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testThatCanSetAppId()
    {
        $session = $this->getSession();
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $this->assertEquals('234', $facebook->getAppId());
        $facebook->setAppId('345');
        $this->assertEquals('345', $facebook->getAppId());
    }

    public function testThatCanSetApiSecret()
    {
        $session = $this->getSession();
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $this->assertEquals('secret', $facebook->getApiSecret());
        $facebook->setApiSecret('secret1');
        $this->assertEquals('secret1', $facebook->getApiSecret());
    }

    public function testThatCanSetAppSecret()
    {
        $session = $this->getSession();
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $facebook->setAppSecret('secret2');
        $this->assertEquals('secret2', $facebook->getAppSecret());
    }

    public function testThatCanSetFileUploadSupport()
    {
        $session = $this->getSession();
        $facebook = new FacebookSessionPersistenceProxy(array('appId' => '234', 'secret' => 'secret'), $session);

        $facebook->setFileUploadSupport(true);
        $this->assertEquals(true, $facebook->getFileUploadSupport());
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
