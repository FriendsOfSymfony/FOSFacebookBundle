<?php

namespace FOS\FacebookBundle\Security\Provider;

use FOS\FacebookBundle\Security\User\User;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Symfony\Component\Security\User\UserProviderInterface;
use Symfony\Component\Security\User\AccountInterface;

class FacebookProvider implements UserProviderInterface
{
    protected $facebook;

    public function __construct(\Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    public function loadUserByUsername($username)
    {
        if (!$uid = $this->getFacebookSession()) {
            throw new UsernameNotFoundException('The user cannot be authenticated');
        }

        return new User($uid, array('ROLE_USER'));
    }

    protected function getFacebookSession()
    {
        try {
            // Find out if a session already exist. If so, check that it is still valid.
            if ($this->facebook->getSession()) {
                // Make sure that session is still valid
                return $this->facebook->getUser();
            }
        } catch (\FacebookApiException $e) {
            
        }

        return false;
    }

    public function loadUserByAccount(AccountInterface $account)
    {
        if (!$account instanceof User) {
            throw new UnsupportedAccountException(sprintf('Instances of "%s" are not supported.', get_class($account)));
        }

        return $this->loadUserByUsername((string) $account);
    }
}