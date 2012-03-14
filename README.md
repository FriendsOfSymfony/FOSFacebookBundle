Introduction
============

Note: this branch is compatible with releases of Symfony2 in the 2.0.x branch.

This Bundle enables integration of the Facebook PHP and JS SDK's. Furthermore it
also provides a Symfony2 authentication provider so that users can login to a
Symfony2 application via Facebook. Furthermore via custom user provider support
the Facebook login can also be integrated with other data sources like the
database based solution provided by FOSUserBundle.

Note that logging in a user requires multiple steps:

  1. the user must be logged into Facebook
  2. the user must connect his Facebook account to your site
  3. once the user has done 1. and 2. you must trigger the login

For step 1. and 2. there are two options:

  1. Select "OAuth Migration" in the Facebook application settings.
  2. Add a Facebook login button, this approach requires JS code to handle step 3.
  3. Letting the FOSFacebookBundle redirecting to the Facebook login page

Note that the later happens automatically if the first provider in your first
firewall configuration is configured to FOSFacebookBundle and the user accesses
a page that requires authentication without being authenticated.

Please also refer to the Facebook documentation:
https://developers.facebook.com/docs/guides/web/

Please also refer to the official documentation of the SecurityBundle, especially
for details on the configuration:
http://symfony.com/doc/2.0/book/security/authentication.html

[![Build Status](https://secure.travis-ci.org/FriendsOfSymfony/FOSFacebookBundle.png?branch=2.0)](http://travis-ci.org/FriendsOfSymfony/FOSFacebookBundle)

Installation
============

  1. Add this bundle and the Facebook PHP SDK to your ``vendor/`` dir:
      * Using the vendors script.

        Add the following lines in your ``deps`` file::

            [FOSFacebookBundle]
                git=git://github.com/FriendsOfSymfony/FOSFacebookBundle.git
                target=/bundles/FOS/FacebookBundle
                version=origin/2.0
            
            [FacebookSDK]
                git=git://github.com/facebook/php-sdk.git
                target=/facebook

        Run the vendors script:

            ./bin/vendors install

      * Using git submodules.

            $ git submodule add git://github.com/FriendsOfSymfony/FOSFacebookBundle.git vendor/bundles/FOS/FacebookBundle
            $ git submodule add git://github.com/facebook/php-sdk.git vendor/facebook

  2. Add the FOS namespace to your autoloader:

          // app/autoload.php
          $loader->registerNamespaces(array(
                'FOS' => __DIR__.'/../vendor/bundles',
                // your other namespaces
          ));

  3. Add this bundle to your application's kernel:

          // app/ApplicationKernel.php
          public function registerBundles()
          {
              return array(
                  // ...
                  new FOS\FacebookBundle\FOSFacebookBundle(),
                  // ...
              );
          }
          
  4. Add the following routes to your application and point them at actual controller actions
          
          #application/config/routing.yml
          _security_check:
              pattern:  /login_check
          _security_logout:
              pattern:  /logout

          #application/config/routing.xml
          <route id="_security_check" pattern="/login_check" />
          <route id="_security_logout" pattern="/logout" />     

  5. Configure the `facebook` service in your config:

          # application/config/config.yml
          fos_facebook:
              file:   %kernel.root_dir%/../vendor/facebook/src/base_facebook.php
              alias:  facebook
              app_id: 123456879
              secret: s3cr3t
              cookie: true
              permissions: [email, user_birthday, user_location]

          # application/config/config.xml
          <fos_facebook:api
              file="%kernel.root_dir%/../vendor/facebook/src/base_facebook.php"
              alias="facebook"
              app_id="123456879"
              secret="s3cr3t"
              cookie="true"
          >
                <permission>email</permission>
                <permission>user_birthday</permission>
                <permission>user_location</permission>
          </fos_facebook:api>

     If you do not include a `file` value in the config you will have to
     configure your application to autoload the `BaseFacebook` class.

  6. Add this configuration if you want to use the `security component`:

          # application/config/config.yml
          security:
              factories:
                  - "%kernel.root_dir%/../vendor/bundles/FOS/FacebookBundle/Resources/config/security_factories.xml"

              firewalls:
                  public:
                      # since anonymous is allowed users will not be forced to login
                      pattern:   ^/.*
                      fos_facebook:
                          app_url: "http://apps.facebook.com/appName/"
                          server_url: "http://localhost/facebookApp/"
                      anonymous: true
                      logout:
                          handlers: ["fos_facebook.logout_handler"]

              access_control:
                  - { path: ^/secured/.*, role: [IS_AUTHENTICATED_FULLY] } # This is the route secured with fos_facebook
                  - { path: ^/.*, role: [IS_AUTHENTICATED_ANONYMOUSLY] }

     You have to add `/secured/` in your routing for this to work. An example would be...
     
              _facebook_secured:
                  pattern: /secured/
                  defaults: { _controller: AcmeDemoBundle:Welcome:index }

  7. Optionally define a custom user provider class and use it as the provider or define path for login

          # application/config/config.yml
          security:
              factories:
                    - "%kernel.root_dir%/../vendor/bundles/FOS/FacebookBundle/Resources/config/security_factories.xml"

              providers:
                  # choose the provider name freely
                  my_fos_facebook_provider:
                      id: my.facebook.user   # see "Example Customer User Provider using the FOS\UserBundle" chapter further down

              firewalls:
                  public:
                      pattern:   ^/.*
                      fos_facebook:
                          app_url: "http://apps.facebook.com/appName/"
                          server_url: "http://localhost/facebookApp/"
                          login_path: ^/login
                          check_path: ^/login_check$
                          default_target_path: /
                          provider: my_fos_facebook_provider
                      anonymous: true
                      logout:
                          handlers: ["fos_facebook.logout_handler"]
          
          # application/config/config_dev.yml
          security:
              firewalls:
                  public:
                      fos_facebook:
                          app_url: "http://apps.facebook.com/appName/"
                          server_url: "http://localhost/facebookApp/app_dev.php/"     

  8. Optionally use access control to secure specific URLs


          # application/config/config.yml
          security:
              # ...
              
              access_control:
                  - { path: ^/facebook/,           role: [ROLE_FACEBOOK] }
                  - { path: ^/.*,                  role: [IS_AUTHENTICATED_ANONYMOUSLY] }
       
    The role `ROLE_FACEBOOK` has to be added in your User class (see Acme\MyBundle\Entity\User::setFBData() below).
    > Note that the order of access control rules matters!

Setting up the JavaScript SDK
-----------------------------

A templating helper is included for loading the Facebook JavaScript SDK and
initializing it with parameters from your service container. To setup the
Facebook JavaScript environment, add the following to your layout just after
the opening `body` tag:

      <body>
          <!-- inside a php template -->
          <?php echo $view['facebook']->initialize(array('xfbml' => true, 'fbAsyncInit' => 'onFbInit();')) ?>
          <!-- inside a twig template -->
          {{ facebook_initialize({'xfbml': true, 'fbAsyncInit': 'onFbInit();'}) }}

Note that `fbAsyncInit` is a parameter helping you to execute JavaScript within 
the function initializing the connection with Facebook, just after the `FB.init();`
call. `onFbInit();` is a JavaScript function defined furthermore to execute functions
which need `FB` initialized.

If you will be adding XFBML markup to your site you may also declare the
namespace, perhaps in the opening `html` tag:

      <html xmlns:fb="http://www.facebook.com/2008/fbml">

Include the login button in your templates
------------------------------------------

Just add the following code in one of your templates:

    <!-- inside a php template -->
    <?php echo $view['facebook']->loginButton(array('autologoutlink' => true)) ?>
    <!-- inside a twig template -->
    {{ facebook_login_button({'autologoutlink': true}) }}

Note that with this approach only the login and connecting with Facebook will
be handled. The step of logging in the user into your Symfony2 application
still needs to be triggered. To do this you will in most cases simply subscribe
to the `auth.statusChange` event and then redirect to the `check_path`:

    <script>
      function goLogIn(){
          window.location = "{{ path('_security_check') }}";
      }
    
      function onFbInit() {
          if (typeof(FB) != 'undefined' && FB != null ) {
              FB.Event.subscribe('auth.statusChange', function(response) {
                  setTimeout(goLogIn, 500);
              });
          }
      }
    </script>
    
Note that we wait 500ms before redirecting to let the browser dealing with the 
Facebook cookie. You can avoid this step but you might get this error message:
*"The Facebook user could not be retrieved from the session."*

The "_security_check" route would need to point to a "/login_check" pattern
to match the above configuration.

Also, you need to trigger the logout action, so, using the same event (`auth.statusChange`), add a simple
check for `response.session` to redirect to the "logout" route:

    <script>
      function goLogIn(){
          window.location = "{{ path('_security_check') }}";
      }
    
      function onFbInit() {
          if (typeof(FB) != 'undefined' && FB != null ) {
              FB.Event.subscribe('auth.statusChange', function(response) {
                  if (response.session || response.authResponse) {
                      setTimeout(goLogIn, 500);
                  } else {
                      window.location = "{{ path('_security_logout') }}";
                  }
              });
          }
      }
    </script>


Example Customer User Provider using the FOS\UserBundle
-------------------------------------------------------

This requires adding a service for the custom user provider which is then set
to the provider id in the "provider" section in the config.yml:

    services:
        my.facebook.user:
            class: Acme\MyBundle\Security\User\Provider\FacebookProvider
            arguments:
                facebook: "@fos_facebook.api"
                userManager: "@fos_user.user_manager"
                validator: "@validator"
                container: "@service_container"

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
            return $this->userManager->findUserBy(array('facebookID' => $fbId));
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
                    $user->setAlgorithm('');
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

Finally one also needs to add a getFacebookId() and setFBData() method to the User model.
The following example also adds "firstname" and "lastname" properties:

    <?php

    namespace Acme\MyBundle\Entity;

    use FOS\UserBundle\Entity\User as BaseUser;

    class User extends BaseUser
    {
        /**
         * @var string
         */
        protected $firstname;

        /**
         * @var string
         */
        protected $lastname;

        /**
         * @var string
         */
        protected $facebookID;


        public function serialize()
        {
            return serialize(array($this->facebookID, parent::serialize()));
        }

        public function unserialize($data)
        {
            list($this->facebookID, $parentData) = unserialize($data);
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
            return $this->getFirstName() . ' ' . $this->getLastname();
        }

        /**
         * @param string $facebookID
         * @return void
         */
        public function setFacebookID($facebookID)
        {
            $this->facebookID = $facebookID;
            $this->setUsername($facebookID);
            $this->salt = '';
        }

        /**
         * @return string
         */
        public function getFacebookID()
        {
            return $this->facebookID;
        }

        /**
         * @param Array
         */
        public function setFBData($fbdata)
        {
            if (isset($fbdata['id'])) {
                $this->setFacebookID($fbdata['id']);
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
