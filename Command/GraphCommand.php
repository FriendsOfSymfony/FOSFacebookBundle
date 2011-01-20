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

class GraphCommand extends Command
{

	private static $allowMethod = array('GET', 'POST', 'DELETE');

	protected function configure()
	{
		parent::configure();

		$this
			->setName('facebook:graph')
			->setDefinition(array(
                new InputArgument('path', InputArgument::REQUIRED, 'Graph path (facebook:graph platform  - get https://graph.facebook.com/platform'),
                new InputOption('method', null, InputOption::VALUE_OPTIONAL, 'HTTP Method ('.implode(',', self::$allowMethod).')', 'GET'),
                new InputOption('access-token', 'at', InputOption::VALUE_OPTIONAL, 'Access token'),
                new InputOption('json', null, InputOption::VALUE_NONE, 'To output result as plain JSON'),
            ))
            ->setDescription('Get properties of an object by requesting graph.facebook.com')
            ->setHelp(<<<EOF
The <info>facebook:graph</info> get properties of an object by requesting https://graph.facebook.com.


For example: Get information about the Facebook Devbelopers Platform
Command get and view file https://graph.facebook.com/platform

  <info>./symfony facebook:graph platform</info>
  
You can also output the information as JSON by using the <comment>--json</comment> option:

  <info>./symfony facebook:graph platform --json</info>
EOF
            );
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
			

		$path = $input->getArgument('path');
		
		$method = $input->getOption('method');
		if (!in_array($method, self::$allowMethod)) {
			throw new InvalidArgumentException('The "method" option has valid value.');
		}
		
		$params = array();
		
		$accessToken = $input->getOption('access-token');
		
		if ($accessToken) {
			$params['access_token'] = $accessToken;
		}
		
		$result = $facebook->api($path, $method, $params);
	
        if ($input->getOption('json')) {
            $output->writeln(json_encode($result), Output::OUTPUT_RAW);
        } else {
            $output->writeln(var_export($result, true));
        }
	}
}