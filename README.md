Installation
============

  1. Add this bundle to your src/ dir and the Facebook PHP SDK to your vendor/ dir:

          $ git submodule add git://github.com/FriendsOfSymfony/FacebookBundle.git src/FOS/FacebookBundle
          $ git submodule add git://github.com/facebook/php-sdk.git vendor/facebook

  2. Add the FOS namespace to your autoloader:

          // src/autoload.php
          $loader->registerNamespaces(array(
                'FOS' => __DIR__,
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

  4. Configure the `facebook` service in your config:

          # application/config/config.yml
          fos_facebook.api:
              file:   %kernel.root_dir%/../vendor/facebook/src/facebook.php
              alias:  facebook
              app_id: 123456879
              secret: s3cr3t
              cookie: true

          # application/config/config.xml
          <fos_facebook:api
              file="%kernel.root_dir%/../vendor/facebook/src/facebook.php"
              alias="facebook"
              app_id="123456879"
              secret="s3cr3t"
              cookie="true"
          />

     If you do not include a `file` value in the config you will have to
     configure your application to autoload the `Facebook` class.

  5. Add this configuration if you want to use the `security component`:

        # application/config/config.yml
          security.config:
              templates:
                  - "%kernel.root_dir%/../src/FOS/FacebookBundle/Resources/config/security_templates.xml"

              providers:
                  fos_facebook:
                    id: fos_facebook.auth

              firewalls:
                  public:
                      pattern:   /.*
                      fos_facebook:  true
                      anonymous: true
                      stateless: true

                  only_facebook: # with facebook entry point (redirect to the facebook login url)
                      pattern:   /only_facebook/.*
                      fos_facebook:  true

              access_control:
                  - { path: /.*, role: [ROLE_USER, IS_AUTHENTICATED_ANONYMOUSLY] }

Setting up the JavaScript SDK
-----------------------------

A templating helper is included for loading the Facebook JavaScript SDK and
initializing it with parameters from your service container. To setup the
Facebook JavaScript environment, add the following to your layout just after
the opening `body` tag:

      <body>
          <!-- inside a php template -->
          <?php echo $view['facebook']->initialize(array('xfbml' => true)) ?>
          <!-- inside a twig template -->
          {{ facebook_initialize({'xfbml': true}) }}

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
