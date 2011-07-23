<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Command;

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
     * @var \FOS\FacebookBundle\Command\ApplicationAccessTokenCommand
     */
    private $applicationAccessTokenCommand;

    /**
     * Get application access token
     *
     * @codeCoverageIgnore
     *
     * @param \BaseFacebook $facebook
     * @return string access token
     */
    public function setApplicationAccessTokenCommand(ApplicationAccessTokenCommand $command)
    {
        $this->applicationAccessTokenCommand = $command;
    }

    /**
     * Get application access token
     *
     * @codeCoverageIgnore
     *
     * @param \BaseFacebook $facebook
     * @return string access token
     */
    protected function getApplicationAccessToken(\BaseFacebook $facebook)
    {
        if (null == $this->applicationAccessTokenCommand) {
            $applicationAccessTokenCommand = new ApplicationAccessTokenCommand();
            $applicationAccessTokenCommand->setContainer($this->getContainer());
            $this->applicationAccessTokenCommand = $applicationAccessTokenCommand;
        }

        return $this->applicationAccessTokenCommand->getAccessToken();
    }
}
