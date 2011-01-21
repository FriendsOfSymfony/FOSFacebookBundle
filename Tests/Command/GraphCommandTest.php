<?php

namespace Bundle\FOS\FacebookBundle\Tests\Command;

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
class GraphCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider methods
     */
    public function simpleRequestByMethod($method)
    {
        $facebook = $this->getMock('Facebook', array('api'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo('platform'), $this->equalTo($method), $this->equalTo(array()))
        ->will($this->returnValue('{id:"1234567890"}'));

        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new GraphCommand();
        $command->setApplication($application);

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:graph', 'path' => 'platform', '--method'=> $method));

        $this->assertRegExp("/1234567890/", $commandTester->getDisplay());
    }

    

    /**
     * @test
     */
    public function accessTokenParam()
    {
        $facebook = $this->getMock('Facebook', array('api'));
        $facebook
        ->expects($this->once())
        ->method('api')
        ->with($this->equalTo('platform'), $this->equalTo('GET'), $this->equalTo(array('access_token' => 'access_token1234567890')))
        ->will($this->returnValue('{id:"1234567890"}'));

        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new GraphCommand();
        $command->setApplication($application);

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:graph', 'path' => 'platform', '--access-token'=> 'access_token1234567890'));

        $this->assertRegExp("/1234567890/", $commandTester->getDisplay());
    }


    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function invalidMethod()
    {
        $facebook = $this->getMock('Facebook', array('api'));

        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new GraphCommand();
        $command->setApplication($application);

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:graph', 'path' => 'platform', '--method'=> 'FAIL'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function requiredArgument()
    {
        $facebook = $this->getMock('Facebook', array('api'));

        $application = new Application(new Kernel());
        $application->getKernel()->getContainer()->set('fos_facebook.api', $facebook);

        $command = new GraphCommand();
        $command->setApplication($application);

        $commandTester = new CommandTester($command);

        $commandTester->execute(array('command' => 'facebook:graph'));
    }



    public function methods()
    {
        return array(array('GET'),array('POST'),array('DELETE'));
    }
}
