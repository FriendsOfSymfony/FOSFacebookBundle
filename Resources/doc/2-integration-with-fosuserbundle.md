Integration with FOSUserBundle
------------------------------

If you still want to use the FOSUserBundle form login, add the "chain_provider" configuration parameter to your ```security.yml```:

>      providers:
>        chain_provider:
>           chain:
>               providers: [fos_userbundle, my_fos_facebook_provider]
>        fos_user_bundle: ...
>        my_fos_facebook_provider: ...

This requires adding a service for the custom user provider, which is then set
to the provider id in the "provider" section in ```config.yml```:
  ```
    services:
        my.facebook.user:
            class: Acme\MyBundle\Security\User\Provider\FacebookProvider
            arguments:
                facebook: "@fos_facebook.api"
                userManager: "@fos_user.user_manager"
                validator: "@validator"

  ```
    <?php

    namespace Acme\MyBundle\Security\User\Provider;

    use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
    use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
    use Symfony\Component\Security\Core\User\UserProviderInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use \BaseFacebook;
    use \FacebookApiException;

    class FacebookProvider implements UserProviderInterface
    {
        /**
         * @var \Facebook
         */
        protected $facebook;
        protected $userManager;
        protected $validator;

        public function __construct(BaseFacebook $facebook, $userManager, $validator)
        {
            $this->facebook = $facebook;
            $this->userManager = $userManager;
            $this->validator = $validator;
        }

        public function supportsClass($class)
        {
            return $this->userManager->supportsClass($class);
        }

        public function findUserByFbId($fbId)
        {
            return $this->userManager->findUserBy(array('facebookId' => $fbId));
        }

        public function loadUserByUsername($username)
        {
            $user = $this->findUserByFbId($username);

            try {
                $fbdata = $this->facebook->api('/me');
            } catch (FacebookApiException $e) {
                $fbdata = null;
            }

            if (!empty($fbdata)) {
                if (empty($user)) {
                    $user = $this->userManager->createUser();
                    $user->setEnabled(true);
                    $user->setPassword('');
                }

                // TODO use http://developers.facebook.com/docs/api/realtime
                $user->setFBData($fbdata);

                if (count($this->validator->validate($user, 'Facebook'))) {
                    // TODO: the user was found obviously, but doesnt match our expectations, do something smart
                    throw new UsernameNotFoundException('The facebook user could not be stored');
                }
                $this->userManager->updateUser($user);
            }

            if (empty($user)) {
                throw new UsernameNotFoundException('The user is not authenticated on facebook');
            }

            return $user;
        }

        public function refreshUser(UserInterface $user)
        {
            if (!$this->supportsClass(get_class($user)) || !$user->getFacebookId()) {
                throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
            }

            return $this->loadUserByUsername($user->getFacebookId());
        }
    }

Finally, one also needs to add getFacebookId() and setFBData() methods to the User model.
The following example also adds "firstname" and "lastname" properties, using the Doctrine ORM:

    <?php

    namespace Acme\MyBundle\Entity;

    use FOS\UserBundle\Entity\User as FOSBaseUser;
    use Doctrine\ORM\Mapping as ORM;

    class User extends FOSBaseUser
    {
        /**
         * @var string
         *
         * @ORM\Column(name="firstname", type="string", length=255)
         */
        protected $firstname;

        /**
         * @var string
         *
         * @ORM\Column(name="lastname", type="string", length=255)
         */
        protected $lastname;

        /**
         * @var string
         *
         * @ORM\Column(name="facebookId", type="string", length=255)
         */
        protected $facebookId;

        public function serialize()
        {
            return serialize(array($this->facebookId, parent::serialize()));
        }

        public function unserialize($data)
        {
            list($this->facebookId, $parentData) = unserialize($data);
            parent::unserialize($parentData);
        }

        /**
         * @return string
         */
        public function getFirstname()
        {
            return $this->firstname;
        }

        /**
         * @param string $firstname
         */
        public function setFirstname($firstname)
        {
            $this->firstname = $firstname;
        }

        /**
         * @return string
         */
        public function getLastname()
        {
            return $this->lastname;
        }

        /**
         * @param string $lastname
         */
        public function setLastname($lastname)
        {
            $this->lastname = $lastname;
        }

        /**
         * Get the full name of the user (first + last name)
         * @return string
         */
        public function getFullName()
        {
            return $this->getFirstname() . ' ' . $this->getLastname();
        }

        /**
         * @param string $facebookId
         * @return void
         */
        public function setFacebookId($facebookId)
        {
            $this->facebookId = $facebookId;
            $this->setUsername($facebookId);
        }

        /**
         * @return string
         */
        public function getFacebookId()
        {
            return $this->facebookId;
        }

        /**
         * @param Array
         */
        public function setFBData($fbdata)
        {
            if (isset($fbdata['id'])) {
                $this->setFacebookId($fbdata['id']);
                $this->addRole('ROLE_FACEBOOK');
            }
            if (isset($fbdata['first_name'])) {
                $this->setFirstname($fbdata['first_name']);
            }
            if (isset($fbdata['last_name'])) {
                $this->setLastname($fbdata['last_name']);
            }
            if (isset($fbdata['email'])) {
                $this->setEmail($fbdata['email']);
            }
        }
    }

Next: [Additional resources](3-another-resources.md)
