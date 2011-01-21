<?php

namespace FOS\FacebookBundle\Security\User;

use Symfony\Component\Security\User\AccountInterface;

/*
 * This file is part of FOSFacebookBundle.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class User implements AccountInterface
{
    protected $username;
    protected $roles;

    public function __construct($username, array $roles = array())
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = (string) $username;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function equals(AccountInterface $account)
    {
        if (!$account instanceof User) {
            return false;
        }

        if ($this->username !== $account->getUsername()) {
            return false;
        }

        return true;
    }
}