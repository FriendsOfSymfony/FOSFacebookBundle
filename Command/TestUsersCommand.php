<?php
namespace Bundle\FOS\FacebookBundle\Command;


use Symfony\Component\Console\Output\Output;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Abstract class of TestUsers commands
 *
 * @author Macin Sikoń <marcin.sikon@gmail.com>
 */
abstract class TestUsersCommand extends Command
{

    const TEST_USERS_PATH = '/accounts/test-users';


    /**
     * ApplicationAccessTokenComand
     *
     * @var \Bundle\FOS\FacebookBundle\Command\ApplicationAccessTokenCommand
     */
    private $applicationAccessTokenCommand;

    
    /**
     * Get application access token
     * 
     * @codeCoverageIgnore
     *
     * @param \Facebook $facebook
     * @return string access token
     */
    public function setApplicationAccessTokenCommand(\Bundle\FOS\FacebookBundle\Command\ApplicationAccessTokenCommand $command) {
        $this->applicationAccessTokenCommand = $command;
    }

    /**
     * Get application access token
     * 
     * @codeCoverageIgnore
     *
     * @param \Facebook $facebook
     * @return string access token
     */
    protected function getApplicationAccessToken(\Facebook $facebook) {
        if (null == $this->applicationAccessTokenCommand) {
            $applicationAccessTokenCommand = new ApplicationAccessTokenCommand();
            $applicationAccessTokenCommand->setFacebook($facebook);

            $this->applicationAccessTokenCommand = $applicationAccessTokenCommand;
        }

        return $this->applicationAccessTokenCommand->getAccessToken();
    }
}