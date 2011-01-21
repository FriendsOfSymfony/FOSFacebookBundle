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
 * List test users associated with your application.
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
class TestUsersListCommand extends TestUsersCommand
{

    protected function configure()
    {
        parent::configure();

        $this
        ->setName('facebook:test-users:list')
        ->setDefinition(array(
        new InputOption('json', null, InputOption::VALUE_NONE, 'To output result as plain JSON'),
        ))
        ->setDescription('List test users associated with your application.')
        ->setHelp(<<<EOF
You can access the test users associated with an application by using the Graph API with the application access token.

API

<comment>GET  /app_id/accounts/test-users</comment>


Response:

<comment>
{
 "data" [
   { 
    "id": "1231....",  
    "access_token":"1223134..." , 
    "login_url":"https://www.facebook.com/platform/test_account.." 
   }
   { 
    "id": "1231....",  
    "access_token":"1223134..." , 
    "login_url":"https://www.facebook.com/platform/test_account.." 
   }
 ]
}
</comment>

<comment>id</comment>
User id of the test user

<comment>access_token</comment>
You can use this access token to make API calls on behalf of the test user. 
This is available only if your application has been installed by the test user.

<comment>login_url</comment>
You can login as the test user by going to this url. 
This expires on first use or after 10 minutes whichever happens first.

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

        $result = $facebook->api($appId.self::TEST_USERS_PATH, 'GET', $params);

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