<?php

namespace Bundle\FOS\FacebookBundle\Tests\Command;

use Bundle\FOS\FacebookBundle\Command\TestUsersCreateCommand;

use Symfony\Component\Console\Input\ArrayInput;

use Bundle\FOS\FacebookBundle\Tests\Kernel;
use Bundle\FOS\FacebookBundle\Command\GraphCommand;
use Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 *
 */
class TestUsersCreateCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider provider
     */
    public function simpleRequest($installed, $permissions, $appId, $accessToken, $params)
    {
        $facebook = $this->getMock('Facebook', array('api','getAppId'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo($appId.'/accounts/test-users'), $this->equalTo('POST'), $this->equalTo($params))
        ->will($this->returnValue('{"id": "1231....","access_token":"1223134...","login_url":"https://www.facebook.com/platform/test_account.."}'));
        

        $facebook
        ->expects($this->once())
        ->method('getAppId')
        ->will($this->returnValue($appId));


        $applicationAccessTokenCommand = $this->getMock('Bundle\\FOS\\FacebookBundle\\Command\\ApplicationAccessTokenCommand', array('getAccessToken'));

        $applicationAccessTokenCommand
        ->expects($this->once())
        ->method('getAccessToken')
        ->will($this->returnValue($accessToken));


        $appication = new Application(new Kernel());
        $command = new TestUsersCreateCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($appication);
        $command->setFacebook($facebook);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:create', '--installed' => $installed, '--permissions' => $permissions ));

        $this->assertRegExp("/login_url/", $commandTester->getDisplay());
    }



    
    
    /**
     * @test
     * @expectedException \FacebookApiException
     */
    public function emptyAppIdConfig()
    {
        $facebook = $this->getMock('Facebook', array('getAppId'));

        $facebook
        ->expects($this->once())
        ->method('getAppId')
        ->will($this->returnValue(null));


        $applicationAccessTokenCommand = $this->getMock('Bundle\\FOS\\FacebookBundle\\Command\\ApplicationAccessTokenCommand', array('getAccessToken'));


        $appication = new Application(new Kernel());
        $command = new TestUsersCreateCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($appication);
        $command->setFacebook($facebook);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:create'));
    }
    
    public function provider()
    {
        return array(
            array(true, 'publish_stream,offline_access', 12345678, 'asdsdfasfasdf', array('installed' => true, 'permissions' => 'publish_stream,offline_access', 'access_token' => 'asdsdfasfasdf')),
            array(false, 'publish_stream', 12345678, 'asdsdfas', array('installed' => false, 'permissions' => 'publish_stream', 'access_token' => 'asdsdfas')),
            array(false, null, 12345678, 'asdsdfasfasdf', array('installed' => false, 'access_token' => 'asdsdfasfasdf')),
            array(false, 'publish_stream', 12345678, 'a', array('installed' => false, 'permissions' => 'publish_stream', 'access_token' => 'a')),
        );
    }
}
