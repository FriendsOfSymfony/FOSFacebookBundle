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

use FOS\FacebookBundle\Facebook\FacebookSessionPersistence;

class FacebookSessionPersistenceTest extends \PHPUnit_Framework_TestCase
{
    public function testThatGetUserSaveUserInSession()
    {
        $session = $this->getSession();
        $session->expects($this->any())
            ->method('set')
            ->with($this->equalTo('_fos_facebook_fb_234_user_id'), $this->equalTo('123456789'));

        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $session);

        $this->assertEquals('123456789', $facebook->getUser());
    }

    public function testThatCanSetAppId()
    {
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $this->assertEquals('234', $facebook->getAppId());
        $facebook->setAppId('345');
        $this->assertEquals('345', $facebook->getAppId());
    }

    public function testThatCanSetApiSecret()
    {
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $this->assertEquals('secret', $facebook->getApiSecret());
        $facebook->setApiSecret('secret1');
        $this->assertEquals('secret1', $facebook->getApiSecret());
    }

    public function testThatCanSetAppSecret()
    {
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook->setAppSecret('secret2');
        $this->assertEquals('secret2', $facebook->getAppSecret());
    }

    public function testThatCanSetFileUploadSupport()
    {
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $this->getSession());

        $facebook->setFileUploadSupport(true);
        $this->assertEquals(true, $facebook->getFileUploadSupport());
    }

    public function testThatCanSetAccessToken()
    {
        $session = $this->getSession();
        $session->expects($this->any())
            ->method('all')
            ->will($this->returnValue(array('1', '_fos_facebook_123' => 'test')));
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $session);

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
        $facebook = new FacebookSessionPersistenceStub(array('appId' => '234', 'secret' => 'secret'), $session);

        $facebook->signedRequest = false;
        $_REQUEST['code'] = '123';
        $_REQUEST['state'] = 'state';
        $this->assertEquals('234|secret', $facebook->getAccessToken());
    }

    private function getSession()
    {
        return $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

class FacebookSessionPersistenceStub extends FacebookSessionPersistence
{
    public $signedRequest = array(
        'user_id' => '123456789'
    );

    public function getSignedRequest()
    {
        return $this->signedRequest;
    }

    public function getCurrentUrl()
    {
        return 'http://localhost';
    }
}
