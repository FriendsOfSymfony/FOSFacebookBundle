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
 * Delete a test user associated with your application.
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
class TestUsersDeleteCommand extends TestUsersCommand
{

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
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \FacebookApiException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $facebook = $this->getFacebook();

        $appId = $facebook->getAppId();

        if (!$appId) {
            throw new \FacebookApiException('Set app_id in config');
        }

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