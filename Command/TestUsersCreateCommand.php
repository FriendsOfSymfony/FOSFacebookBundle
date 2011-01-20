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

class TestUsersCreateCommand extends Command
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
		->setName('facebook:test-users:create')
		->setDefinition(array(
		new InputOption('installed', 'i', InputOption::VALUE_OPTIONAL, 'This is a Boolean parameter to specify whether your application should be installed for the test user at the time of creation. It is true by default.'),
		new InputOption('permissions', 'p', InputOption::VALUE_OPTIONAL, 'This is a comma-separated list of extended permissions(http://developers.facebook.com/docs/authentication/permissions). Your application is granted these permissions for the new test user if ‘installed’ is true.'),
		new InputOption('json', null, InputOption::VALUE_NONE, 'To output result as plain JSON'),
		))
		->setDescription('Create a test user associated with your application.')
		->setHelp(<<<EOF
You can create a test user associated with a particular application using the Graph API with the application access token.
 
<comment>POST /app_id/accounts/test-users?installed=true&permissions=read_stream</comment>

Parameters:

You can specify whether this user has already installed your application 
as well as the set of permissions that your application is granted for 
this user by default upon creation.

<comment>installed</comment> 
This is a Boolean parameter to specify whether your
application should be installed for the test user 
at the time of creation. It is true by default.
                             
<comment>permissions</comment>
This is a comma-separated list of extended permissions.

Your application is granted these permissions for the new test user if ‘installed’ is true.
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
		
		
			
		$params = array('installed' => (bool) $input->getOption('installed'), 'access_token' => $this->getApplicationAccessToken($facebook));

		$permissions = $input->getOption('permissions');
		if ($permissions) {
			$params['permissions'] = $permissions;
		}
		

		$result = $facebook->api($appId.self::$testUsersPath, 'POST', $params);

		if ($input->getOption('json')) {
			$output->writeln(json_encode($result), Output::OUTPUT_RAW);
		} else {
			$output->writeln('New test user:');
			$output->writeln('');
			$output->writeln('id:              <comment>'.$result['id'].'</comment>');
			$output->writeln('access_token:    <comment>'.$result['access_token'].'</comment>');
			$output->writeln('login_url:       <comment>'.$result['login_url'].'</comment>');
			$output->writeln('');
		}
	}
}