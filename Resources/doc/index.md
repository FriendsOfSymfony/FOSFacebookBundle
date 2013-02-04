Getting start with FOSFacebookBundle
====================================


To start with FOSFacebookBundle, it's important to understand the Facebook's login process, that requires the follow steps:

1. the user must be logged into Facebook
2. the user must connect his Facebook account to your app
3. once the user has done 1. and 2. your app must trigger the login

Before continue, you should go in your application settings on Facebook Developers page and select the option "OAuth Migration". You will have problems if you don't enable it.

To handle with the authentication you have two options:

* Use the JS SDK to authenticate the user on the client side;
* Letting FOSFacebookBundle redirect to the Facebook login page

Note that the later happens automatically if the first provider in your first
firewall configuration is configured to FOSFacebookBundle and the user access
a page that requires authentication without being authenticated.

Before you go ahead and use FOSFacebookBundle you should considerer take a look at Facebook and Symfony SecurityBundle documentation:
* [Facebook documentation](https://developers.facebook.com/docs/guides/web/)
* [SecurityBundle documentation](http://symfony.com/doc/current/book/security.html)



Usage guide
-----------

1. [Basic usage](https://github.com/FriendsOfSymfony/FOSFacebookBundle/blob/master/Resources/doc/1-basic-usage.md)
2. [Integration with FOSUserBundle](https://github.com/FriendsOfSymfony/FOSFacebookBundle/blob/master/Resources/doc/2-integration-with-fosuserbundle.md)
3. [Another resources](https://github.com/FriendsOfSymfony/FOSFacebookBundle/blob/master/Resources/doc/3-another-resources.md)
4. [Example Server-Side Facebook Login using Mobile Apps](https://github.com/FriendsOfSymfony/FOSFacebookBundle/blob/master/Resources/doc/4-example-server-side-facebook-login-using-mobile-apps.md)
