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

class TestUsersDeleteCommand extends Command
{

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
		->setName('facebook:test-users:delete')
		->setDefinition(array(
		    new InputArgument('test_user_id', InputArgument::REQUIRED, 'User id'),
			new InputOption('json', null, InputOption::VALUE_NONE, 'To output result as plain JSON'),
		))
		->setDescription('Delete a test user associated with your application.')
		->setHelp(<<<EOF
You can delete an existing test user like any other object in the graph.

API
<comment>DELETE  /test_user_id</comment>

with access token of the test user.
Response: true on success, false otherwise

EOF
			);
		
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


		$result = $facebook->api($input->getArgument('test_user_id'), 'DELETE', $params);

		if ($input->getOption('json')) {
			$output->writeln(json_encode($result), Output::OUTPUT_RAW);
		} else {
			if ($result) {
				$output->writeln('User was deleted.');
			} else {
				$output->writeln('User wasn\'t deleted.');
			}
		}
	}
}