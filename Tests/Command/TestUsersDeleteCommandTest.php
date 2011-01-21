<?php

namespace Bundle\FOS\FacebookBundle\Tests\Command;

use Bundle\FOS\FacebookBundle\Command\TestUsersDeleteCommand;
use Bundle\FOS\FacebookBundle\Tests\Kernel;
use Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 *
 */
class TestUsersDeleteCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider provider
     */
    public function simpleRequest($userId, $appId, $accessToken, $json, $params, $resultApi, $result)
    {
        $facebook = $this->getMock('Facebook', array('api','getAppId'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo($userId), $this->equalTo('DELETE'), $this->equalTo($params))
        ->will($this->returnValue($resultApi));


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
        $command = new TestUsersDeleteCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($appication);
        $command->setFacebook($facebook);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:delete', 'test_user_id' => $userId, '--json' => $json));

        $this->assertRegExp('/'.$result.'/', $commandTester->getDisplay());
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
        $command = new TestUsersDeleteCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($appication);
        $command->setFacebook($facebook);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:delete', 'test_user_id' => 123));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function requiredArgument()
    {
        $facebook = $this->getMock('Facebook', array('getAppId'));


        $applicationAccessTokenCommand = $this->getMock('Bundle\\FOS\\FacebookBundle\\Command\\ApplicationAccessTokenCommand', array('getAccessToken'));


        $appication = new Application(new Kernel());
        $command = new TestUsersDeleteCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($appication);
        $command->setFacebook($facebook);
        
        


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:delete'));
    }

    public function provider()
    {
        return array(
        array(1, 12345678, 'asdsdfasfasdf', true, array('access_token' => 'asdsdfasfasdf'), true, 'true'),
        array(2, 12345678,'asdsdfas',true, array('access_token' => 'asdsdfas'), false, 'false'),
        array(3, 12345678,'g', false, array('access_token' => 'g'), true, 'was'),
        array(6, 12345678,'a',false, array('access_token' => 'a'), false, 'wasn'),
        );
    }
}
