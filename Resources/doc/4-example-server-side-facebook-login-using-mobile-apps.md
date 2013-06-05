Example Server-Side Facebook Login using Mobile Apps
----------------------------------------------------

If you use the JavaScript SDK to login by web, the normal flow would be as follows:

  1. Click Facebook login button from your page.
  2. It will go to Facebook OAuth API

  ```
https://www.facebook.com/dialog/oauth?client_id=<client_id>&redirect_uri=<redirect_uri>&state=<state>&scope=<scope>
  ```        
  3. After user accepts the Facebook application, it will go back to your site's `redirect_uri`:

  ```
https://localhost/facebookApp/login_check?state=<state>&code=<code>#_=_
  ```        

This flow is also described by [Technical Guide from Facebook Developers](https://developers.facebook.com/docs/howtos/login/server-side-login/).

However, if you are developing server-side Facebook login using mobile apps for
Apple iOS or Google Android, their respective SDKs obtain access tokens from
mobile devices. In this case, the only API call required from mobile side is:

  ```
https://localhost/facebookApp/login_check?access_token=<access_token>
  ```        

__ATTENTION__: Since an access token provides secure access to Facebook APIs on
behalf of the user, we must always pass the token using HTTPS. Please read
the section Sharing of Access Tokens from [Facebook Developers](https://developers.facebook.com/docs/concepts/login/access-tokens-and-types/)
before using this mechanism.
