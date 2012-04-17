<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Security\Authentication\Provider;

use FOS\FacebookBundle\Security\User\UserManagerInterface;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

use FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken;

class FacebookProvider implements AuthenticationProviderInterface
{
    /**
     * @var \BaseFacebook
     */
    protected $facebook;
    protected $providerKey;
    protected $userProvider;
    protected $userChecker;
    protected $createIfNotExists;

    public function __construct($providerKey, \BaseFacebook $facebook, UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null, $createIfNotExists = false)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        if ($createIfNotExists && !$userProvider instanceof UserManagerInterface) {
            throw new \InvalidArgumentException('The $userProvider must implement UserManagerInterface if $createIfNotExists is true.');
        }

        $this->providerKey = $providerKey;
        $this->facebook = $facebook;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->createIfNotExists = $createIfNotExists;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $this->userChecker->checkPostAuth($user);

            $newToken = new FacebookUserToken($this->providerKey, $user, $user->getRoles());
            $newToken->setAttributes($token->getAttributes());

            return $newToken;
        }

        try {
            if ($uid = $this->facebook->getUser()) {
                $newToken = $this->createAuthenticatedToken($uid);
                $newToken->setAttributes($token->getAttributes());

                return $newToken;
            }
        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException($failed->getMessage(), null, (int)$failed->getCode(), $failed);
        }

        throw new AuthenticationException('The Facebook user could not be retrieved from the session.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof FacebookUserToken && $this->providerKey === $token->getProviderKey();
    }

    protected function createAuthenticatedToken($uid)
    {
        if (null === $this->userProvider) {
            return new FacebookUserToken($this->providerKey, $uid);
        }

        try {
            $user = $this->userProvider->loadUserByUsername($uid);
            $this->userChecker->checkPostAuth($user);
        } catch (UsernameNotFoundException $ex) {
            if (!$this->createIfNotExists) {
                throw $ex;
            }

            $user = $this->userProvider->createUserFromUid($uid);
        }

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        return new FacebookUserToken($this->providerKey, $user, $user->getRoles());
    }
}
