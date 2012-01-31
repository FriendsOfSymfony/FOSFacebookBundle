<?php

namespace FOS\FacebookBundle\Facebook;

interface FacebookInterface
{
    function getUser();
    function getAppId();
    function setAppId($appId);
    function getAppSecret();
    function setAppSecret($secret);
    function getApiSecret();
    function setApiSecret($secret);
    function setFileUploadSupport($supported);
    function getFileUploadSupport();
    function getAccessToken();
    function setAccessToken($token);
    function getSignedRequest();
    function getLoginUrl($params = array());
    function getLogoutUrl($params = array());
    function getLoginStatusUrl($params = array());
    function api();
    function destroySession();
}
