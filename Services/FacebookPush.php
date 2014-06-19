<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\FacebookBundle\Model\StreamPost as BaseMessage;
use \BaseFacebook;
use \FacebookApiException;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 *
 * This class provides a service that abstracts Facebook API functionality such as retrieving user information and
 * posting data to Facebook API.
 * @author Teemu Reisbacka <teemu.reisbacka@gmail.com>
 *
 */
class FacebookPush
{
    protected $facebook;
    protected $errorMessage;

    public function __construct(BaseFacebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Returns error message if an executed function returned a failure (false value).
     *
     * @return string
     */
    public function getErrorMessage()
    {

        return empty($this->errorMessage) ? '' : $this->errorMessage;
    }

    /**
     * Retrieves available user information from Facebook for the logged in user. Available information
     * depends on what access privileges the user has granted to your app.
     *
     * @return array|null
     */
    public function getUserInfromation()
    {
        $accessToken = $this->facebook->getAccessToken();
        /*	Adding the locale parameter is important, because otherwise Facebook api might localize some
         * 	variable values, such as gender.
         */
        $me = json_decode(file_get_contents("https://graph.facebook.com/me?access_token={$accessToken}&locale=en_US"), true);

        return $me;
    }

    /**
     * Retrieves the profile picture of the logged in user in binary format, so that you can
     * save it locally.
     *
     * @return string|boolean
     */
    public function getProfilePicture()
    {
        $facebookUID = $this->facebook->getUser();
        if (empty($facebookUID)) {
            return null;
        }

        $binaryImage = file_get_contents("http://graph.facebook.com/{$facebookUID}/picture?type=large");

        return $binaryImage;
    }

    /**
     * Publishes a message to user's Facebook stream as the user. You application name will be displayed at the bottom
     * of the message.
     * REQUIRES Facebook access permission "publish_stream"
     *
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
            throw new InvalidArgumentException('No facebook access token, cannot post to stream');
        }

        $streamPost->setAccessToken($accessToken);
        $message = $streamPost->formatData();
        try {
            $result = $this->facebook->api( '/me/feed/', 'post', $message );

            /*	Confirm that the post went through -> Facebook api return message id
             */
            if (!is_array($result) || empty($result["id"])) {
                $this->errorMessage = "Did not receive message id back from the api, post failed.";
                return false;
            }
        } catch (FacebookApiException $e) {
            $this->errorMessage = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Publishes a message to user's Facebook page as the page.
     * REQUIRES Facebook access permission "manage_pages"
     *
     * @param Rohea\FacebookBundle\Models\streamPost $streamPost Stream post object
     * @param string $pageID Your page facebook id. You can see this for examnple in your browser uri-bar when browsing the page.
     * @return boolean
     */
    public function publishPage(BaseMessage $streamPost, $pageID)
    {
        try {
            $accessToken = $this->getPageAccessToken($pageID);
            $streamPost->setAccessToken($accessToken);
            $message = $streamPost->formatData();
            $result = $this->facebook->api( "/{$pageID}/feed/", 'post', $message );

            /*	Confirm that the post went through -> Facebook api return message id
             */
            if (!is_array($result) || empty($result["id"])) {
                $this->errorMessage = "Did not receive message id back from the api, post failed.";
                return false;
            }
        } catch (FacebookApiException $e) {
            $this->errorMessage = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Attempts to query access token for give Facebook page
     * REQUIRES Facebook access permission "manage_pages"
     *
     * @param string $pageID Facebook page id
     * @throws \UnexpectedValueException
     * @return string
     */
    public function getPageAccessToken($pageID)
    {
        $pageInfo = $this->facebook->api("/$pageID?fields=access_token");

        if (empty($pageInfo['access_token'])) {
            throw new UnexpectedValueException("Could not retrieve access token for the page $pageID");
        }

        return $pageInfo['access_token'];
    }
}