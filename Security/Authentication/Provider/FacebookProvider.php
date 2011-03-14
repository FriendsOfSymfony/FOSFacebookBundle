<?php

namespace FOS\FacebookBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

use FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken;
use \Facebook;

class FacebookProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Facebook
     */
    protected $facebook;
    protected $userProvider;
    protected $userChecker;

    public function __construct(Facebook $facebook, UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        $this->facebook = $facebook;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        try {
            if ($uid = $this->facebook->getUser()) {
                return $this->createAuthenticatedToken($uid);
            }
        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException('Unknown error', $failed->getMessage(), $failed->getCode(), $failed);
        }

        throw new AuthenticationException('The Facebook user could not be retrieved from the session.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof FacebookUserToken;
    }

    protected function createAuthenticatedToken($uid)
    {
        if (null === $this->userProvider) {
            throw new AuthenticationException('No Facebook user provider available.');
        }

        $user = $this->userProvider->loadUserByUsername($uid);
        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);

        return new FacebookUserToken($user, $user->getRoles());
    }

    /**
     * Refresh a user
     *
     * @param UserInterface $user
     */
    public function loadUser(UserInterface $user)
    {
        throw new UnsupportedUserException('User is not supported.');
    }
}
