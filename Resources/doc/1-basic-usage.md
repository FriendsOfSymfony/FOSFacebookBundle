Basic usage
===========


1. Add the following lines in your composer.json:

  ```yaml
  {
    "require": {
      "friendsofsymfony/facebook-bundle": "1.1.*"
    }
  }
  ```

2. Run the composer to download the bundle
  ```bash
  $ php composer.phar update friendsofsymfony/facebook-bundle
  ```


3. Add this bundle to your application's kernel:

  ```php
  // app/ApplicationKernel.php
  public function registerBundles()
  {
        return array(
            // ...
            new FOS\FacebookBundle\FOSFacebookBundle(),
            // ...
        );
  }
  ```

4. Add the following routes to your application and point them at actual controller actions

  ```yaml
  #application/config/routing.yml
  _security_check:
      pattern:  /login_check
  _security_logout:
      pattern:  /logout
  ```

  ```xml
  #application/config/routing.xml
  <route id="_security_check" pattern="/login_check" />
  <route id="_security_logout" pattern="/logout" />
  ```

5. Configure the `facebook` service in your config:
  ```yaml
  # application/config/config.yml
  fos_facebook:
      alias:  facebook
      app_id: 123456879
      secret: s3cr3t
      cookie: true
      permissions: [email, user_birthday, user_location]
  ```

  ```xml
  # application/config/config.xml
  <fos_facebook:api
      alias="facebook"
      app_id="123456879"
      secret="s3cr3t"
      cookie="true"
  >
      <permission>email</permission>
      <permission>user_birthday</permission>
      <permission>user_location</permission>
  </fos_facebook:api>
  ```


6. Add this configuration if you want to use the `security component`:

  ```yaml
          # application/config/security.yml
          security:
              firewalls:
                  public:
                      # since anonymous is allowed users will not be forced to login
                      pattern:   ^/.*
                      fos_facebook:
                          app_url: "http://apps.facebook.com/appName/"
                          server_url: "http://localhost/facebookApp/"
                      anonymous: true

              access_control:
                  - { path: ^/secured/.*, role: [IS_AUTHENTICATED_FULLY] } # This is the route secured with fos_facebook
                  - { path: ^/.*, role: [IS_AUTHENTICATED_ANONYMOUSLY] }
  ```

     You have to add `/secured/` in your routing for this to work. An example would be...
     
    ```yaml
    _facebook_secured:
        pattern: /secured/
        defaults: { _controller: AcmeDemoBundle:Welcome:index }
    ```

7. Optionally define a custom user provider class and use it as the provider or define path for login
  ```yaml
    # application/config/config.yml
    security:
        providers:
            # choose the provider name freely
            my_fos_facebook_provider:
                id: my.facebook.user   # see "Example Custom User Provider using the FOS\UserBundle" chapter further down

        firewalls:
            public:
                pattern: ^/.*
                fos_facebook:
                    app_url: "http://apps.facebook.com/appName/"
                    server_url: "http://localhost/facebookApp/"
                    login_path: /login
                    check_path: /login_check
                    default_target_path: /
                    provider: my_fos_facebook_provider
                anonymous: true

    # application/config/config_dev.yml
    security:
        firewalls:
            public:
                fos_facebook:
                    app_url: "http://apps.facebook.com/appName/"
                    server_url: "http://localhost/facebookApp/app_dev.php/"     
  ```

8. Optionally use access control to secure specific URLs

    ```yaml
    # application/config/config.yml
    security:
        # ...
        
        access_control:
            - { path: ^/facebook/,           role: [ROLE_FACEBOOK] }
            - { path: ^/.*,                  role: [IS_AUTHENTICATED_ANONYMOUSLY] }
    ```
       
    The role `ROLE_FACEBOOK` has to be added in your User class (see Acme\MyBundle\Entity\User::setFBData() below).
    > Note that the order of access control rules matters!


Setting up the JavaScript SDK
-----------------------------

A templating helper is included for loading the Facebook JavaScript SDK and
initializing it with parameters from your service container. To setup the
Facebook JavaScript environment, add the following to your layout just after
the opening `body` tag:
```php
<?php // inside a php template ?>
<?php echo $view['facebook']->initialize(array('xfbml' => true, 'fbAsyncInit' => 'onFbInit();')) ?>
```
```html+jinja
<!-- inside a twig template -->
{{ facebook_initialize({'xfbml': true, 'fbAsyncInit': 'onFbInit();'}) }}
```
Note that `fbAsyncInit` is a parameter helping you to execute JavaScript within 
the function initializing the connection with Facebook, just after the `FB.init();`
call. `onFbInit();` is a JavaScript function defined furthermore to execute functions
which need `FB` initialized.

If you will be adding XFBML markup to your site you may also declare the
namespace, perhaps in the opening `html` tag:
```html
<html xmlns:fb="http://www.facebook.com/2008/fbml">
```
Include the login button in your templates
------------------------------------------

Add the following code in one of your templates:
```php
<?php // inside a php template ?>
<?php echo $view['facebook']->loginButton(array('autologoutlink' => true)) ?>
```
```jinja
{# inside a twig template #}
{{ facebook_login_button({'autologoutlink': true}) }}
```
If you want customize the the login button, you can set these parameters:

  - label     : The text that shows in the button.
  - showFaces : Specifies whether to show faces underneath the Login button.
  - width     : The width of the plugin in pixels. Default width: 200px.
  - maxRows   : The maximum number of rows of profile pictures to display. Default value: 1.
  - scope     : A comma separated list of extended permissions.
  - registrationUrl : Registration page url. If the user has not registered for your site, they will be redirected to the URL you specify in the registrationUrl parameter.
  - size      : Different sized buttons: small, medium, large, xlarge (default: medium).


Note that with this approach, only the login and connecting with Facebook will
be handled. The step of logging in the user into your Symfony2 application
still needs to be triggered. To do this you, will in most cases simply subscribe
to the `auth.statusChange` event and then redirect to the `check_path`:
```html+jinja
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
```  
Note that we wait 500ms before redirecting to let the browser dealing with the 
Facebook cookie. You can avoid this step, but you might get this error message:
*"The Facebook user could not be retrieved from the session."*

The `_security_check` route would need to point to a `/login_check` pattern
to match the above configuration.

Also, you need to trigger the logout action, so, using the same event (`auth.statusChange`), add a simple
check for `response.session` to redirect to the `logout` route:
```html+jinja
<script>
    function goLogIn(){
        window.location.href = "{{ path('_security_check') }}";
    }
    
    function onFbInit() {
        if (typeof(FB) != 'undefined' && FB != null ) {              
            FB.Event.subscribe('auth.statusChange', function(response) {
                if (response.session || response.authResponse) {
                    setTimeout(goLogIn, 500);
                } else {
                    window.location.href = "{{ path('_security_logout') }}";
                }
            });
        }
    }
</script>
```

