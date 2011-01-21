<?php

namespace Bundle\FOS\FacebookBundle\Tests\Command;

use Bundle\FOS\FacebookBundle\Command\TestUsersListCommand;
use Bundle\FOS\FacebookBundle\Tests\Kernel;
use Bundle\FOS\FacebookBundle\DependencyInjection\FacebookExtension;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 *
 */
class TestUsersDeleteListTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider simpleRequestProvider
     */
    public function simpleRequest($json, $result)
    {
        $appId = '1234567890';
        $accessToken = 'accesstoken';



        $facebook = $this->getMock('Facebook', array('api','getAppId'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo($appId.'/accounts/test-users'), $this->equalTo('GET'), $this->equalTo(array('access_token' => $accessToken)))
        ->will($this->returnValue(array("data" => array(array("id"=> "1231....","access_token"=>"1223134...","login_url"=>"https://www.facebook.com/platform/test_account..")))));


        $facebook
        ->expects($this->once())
        ->method('getAppId')
        ->will($this->returnValue($appId));


        $applicationAccessTokenCommand = $this->getMock('Bundle\\FOS\\FacebookBundle\\Command\\ApplicationAccessTokenCommand', array('getAccessToken'));

        $applicationAccessTokenCommand
        ->expects($this->once())
        ->method('getAccessToken')
        ->will($this->returnValue($accessToken));


        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new TestUsersListCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($application);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:list', '--json' => $json));

        $this->assertRegExp('/'.$result.'/', $commandTester->getDisplay());
    }



    /**
     * @test
     * @dataProvider emptyResultProvider
     */
    public function emptyResult($json, $result)
    {
        $appId = '1234567890';
        $accessToken = 'accesstoken';



        $facebook = $this->getMock('Facebook', array('api','getAppId'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo($appId.'/accounts/test-users'), $this->equalTo('GET'), $this->equalTo(array('access_token' => $accessToken)))
        ->will($this->returnValue(array('data' => array())));


        $facebook
        ->expects($this->once())
        ->method('getAppId')
        ->will($this->returnValue($appId));


        $applicationAccessTokenCommand = $this->getMock('Bundle\\FOS\\FacebookBundle\\Command\\ApplicationAccessTokenCommand', array('getAccessToken'));

        $applicationAccessTokenCommand
        ->expects($this->once())
        ->method('getAccessToken')
        ->will($this->returnValue($accessToken));


        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new TestUsersListCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($application);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:list', '--json' => $json));

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


        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new TestUsersListCommand();
        $command->setApplicationAccessTokenCommand($applicationAccessTokenCommand);
        $command->setApplication($application);


        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:test-users:list'));
    }


    public function simpleRequestProvider()
    {
        return array(
            array(true,'"id":'),
            array(false, 'id: '),
        );
    }
    
    public function emptyResultProvider()
    {
        return array(
            array(true,'"data"'),
            array(false, 'Empty'),
        );
    }
}
