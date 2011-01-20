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

class ApplicationAccessTokenCommand extends Command
{
	static private $oathAccessTokenLocation = 'https://graph.facebook.com/oauth/access_token';


	protected function configure()
	{
		parent::configure();

		$this
		->setName('facebook:application-access-token')
		->setDefinition(array(
		new InputOption('plain', null, InputOption::VALUE_NONE, 'To output result as plain text'),
		))
		->setDescription('Get application access token');
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
		$result = $this->getAccessToken();

		if ($input->getOption('plain')) {
			$output->writeln($result, Output::OUTPUT_RAW);
		} else {
			$output->writeln('Access Token:');
			$output->writeln('');
			$output->writeln('<comment>'.$result.'</comment>');
			$output->writeln('');
		}
	}


	/**
	 * Get application access token
	 * 
	 * @return string access token
	 * @throws \FacebookApiException
	 */
	public function getAccessToken() {
		$facebook = $this->container->get('fos_facebook.api');
			
		$params = array('grant_type' => 'client_credentials', 'client_id' => $facebook->getAppId(), 'client_secret' => $facebook->getApiSecret());

		$ch = curl_init();

		$opts = array();
		$opts[CURLOPT_URL] = self::$oathAccessTokenLocation;
		$opts[CURLOPT_POSTFIELDS] = $params;
		$opts[CURLOPT_RETURNTRANSFER] = true;

		curl_setopt_array($ch, $opts);

		$result = curl_exec($ch);
		curl_close($ch);

		if (!$result) {
			throw new \FacebookApiException(array('error_description' => 'Error while get access token'));
		}

		$resultArray = explode('=', $result);

		if (count($resultArray) != 2 || $resultArray[0] != 'access_token') {
			throw new \FacebookApiException(array('error_description' => 'Invalid response'));
		}

		return $resultArray[1];
	}
}