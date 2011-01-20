<?php
namespace Bundle\FOS\FacebookBundle\Command;


use Symfony\Component\Console\Output\Output;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestUsersListCommand extends Command
{

	static private $testUsersPath = '/accounts/test-users';


	/**
	 * ApplicationAccessTokenCommand for get access_token
	 *
	 * @var ApplicationAccessTokenCommand
	 */
	private $applicationAccessTokenCommand;

	protected function configure()
	{
		parent::configure();

		$this
		->setName('facebook:test-users:list')
		->setDefinition(array(
		new InputOption('json', null, InputOption::VALUE_NONE, 'To output result as plain JSON'),
		))
		->setDescription('List test users associated with your application.');
	}


	/**
	 * @param ApplicationAccessTokenCommand $command
	 */
	public function setApplicationAccessTokenCommand(ApplicationAccessTokenCommand $command) {
		$this->applicationAccessTokenCommand = $command;
	}

	/**
	 * @return ApplicationAccessTokenCommand
	 */
	public function getApplicationAccessTokenCommand() {
		if (null == $this->applicationAccessTokenCommand) {
			return new ApplicationAccessTokenCommand();
		}
		return $this->applicationAccessTokenCommand;
	}



	private function getApplicationAccessToken(\Facebook $facebook) {
		$applicationAccessTokenCommand = $this->getApplicationAccessTokenCommand();
		$applicationAccessTokenCommand->setFacebook($facebook);

		return $applicationAccessTokenCommand->getAccessToken();
	}



	/**
	 * Executes the current command.
	 *
	 * @param InputInterface  $input  An InputInterface instance
	 * @param OutputInterface $output An OutputInterface instance
	 *
	 * @throws \FacebookApiException
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$facebook = $this->container->get('fos_facebook.api');


		$appId = $facebook->getAppId();

		$params = array('access_token' => $this->getApplicationAccessToken($facebook));


		$result = $facebook->api($appId.self::$testUsersPath, 'GET', $params);

		if ($input->getOption('json')) {
			$output->writeln(json_encode($result), Output::OUTPUT_RAW);
		} else {
			if (empty($result['data'])) {
				$output->writeln('Empty result. use facebook:test-users:create');
			} else {
				$output->writeln('Test Users:');
				$output->writeln('');
				foreach ($result['data'] as $user) {
					$output->writeln('id:              <comment>'.$user['id'].'</comment>');
					$output->writeln('login_url:       <comment>'.$user['login_url'].'</comment>');
					$output->writeln('---------------------------------------------------------------------------------');
				}
				$output->writeln('');
			}
		}
	}
}