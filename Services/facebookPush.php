<?php
/*
 * This file is part of FOSFacebookBundle.
 *
 * (c) Teemu Reibacka <teemu.reisbacka@gmail.com>
 *
 * File provides a service that abstracts Facebook API functionality such as retrieving user information and
 * posting data to Facebook API.
 */
namespace FOS\FacebookBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\FacebookBundle\Models\streamPost as BaseMessage;
use \BaseFacebook;

class facebookPush
{
    protected $facebook;
    protected $errorMessage;

    public function __construct(BaseFacebook $facebook) {
        $this->facebook = $facebook;
    }
    /**
     * Returns error message if an executed function returned a failure (false value).
     * @return string
     */
    public function getErrorMessage(){

        return empty($this->errorMessage) ? '' : $this->errorMessage;
    }

    /**
     * Retrieves available user information from Facebook for the logged in user. Available information
     * depends on what access privileges the user has granted to your app.
     * @return string|null
     */
    public function getUserInfromation()
    {
        $access_token = self::get_access_token();

        try {
        /*	Adding the locale parameter is important, because otherwise Facebook api might localize some
         * 	variable values, such as gender.
         */
            $me = json_decode(file_get_contents("https://graph.facebook.com/me?access_token={$access_token}&locale=en_US"));
        }
        catch (\Exception $e)
        {
            $this->errorMessage = $e->getMessage();

            return null;
        }

        return $me;
    }
    /**
     * Retrieves the profile picture of the logged in user in binary format, so that you can
     * save it locally.
     * @return string|null
     */
    public static function getProfilePicture()
    {
        $facebookUID = $this->facebook->getUser();

        if (empty($facebookUID)) {

            return null;
        }

        try {
            $binary_image = file_get_contents("http://graph.facebook.com/{$facebookUID}/picture?type=large");
            return $binary_image;
        }
        catch (\Exception $e)
        {
            $this->errorMessage = $e->getMessage();

            return null;
        }
    }
    /**
     * Publishes a message to user's Facebook stream as the user. You application name will be displayed at the bottom
     * of the message.
     * REQUIRES Facebook access permission "publish_stream"
     * @param Rohea\FacebookBundle\Models\streamPost $streamPost Stream post object
     * @param string $accessToken Optional Facebook Access token, if not given, logged in user is used
     * @return boolean
     */

    public function publishStream(BaseMessage $streamPost, $accessToken = null)
    {
        if (empty($accessToken)) {
            $accessToken = $this->facebook->getAccessToken();
        }
        if (empty($accessToken)) {
            throw new \Exception('No facebook access token, cannot post to stream');
        }

        $streamPost->setAccessToken($accessToken);
        $message = $streamPost->formatData();

        try
        {
            $result = $this->facebook->api( '/me/feed/', 'post', $message );
        }
        catch (\Exception $e)
        {
            $this->errorMessage = $e->getMessage();

            return false;
        }

        return true;
    }
    /**
     * Publishes a message to user's Facebook page as the page.
     * REQUIRES Facebook access permission "manage_pages"
     * @param Rohea\FacebookBundle\Models\streamPost $streamPost Stream post object
     * @param string $pageID Your page facebook id. You can see this for examnple in your browser uri-bar when browsing the page.
     * @return boolea
     */
    public function publishPage(BaseMessage $streamPost, $pageID)
    {
        try
        {
            $accessToken = $this->getPageAccessToken($pageID);
            $streamPost->setAccessToken($accessToken);
            $message = $streamPost->formatData();
            $result = $this->facebook->api( "/{$pageID}/feed/", 'post', $message );
        }
        catch (\Exception $e)
        {
            $this->errorMessage = $e->getMessage();

            return true;
        }

        return true;
    }
    /**
     * Attempts to query access token for give Facebook page
     * REQUIRES Facebook access permission "manage_pages"
     * @param string $pageID Facebook page id
     * @throws \Exception
     * @return string
     */
    public function getPageAccessToken($pageID)
    {
        $page_info = $this->facebook->api("/$pageID?fields=access_token");

        if( empty($page_info['access_token']) ) {
            throw new \Exception("Could not retrieve access token for the page $pageID");
        }

        return $page_info['access_token'];
    }
}
