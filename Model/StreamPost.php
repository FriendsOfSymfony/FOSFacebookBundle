<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Model;

use \InvalidArgumentException;

/**
 *
 * This class functions as a model for messages to be sent by the facebookPush-service to the Facebook api.
 * @author Teemu Reisbacka <teemu.reisbacka@gmail.com>
 *
 */
class StreamPost
{
    protected $accessToken;
    protected $message;
    protected $attachment;
    protected $linkout;

    /**
     * Sets the access token used in message body. This is set automatically inside facebookPush-service.
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Sets the actual text content of the message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Adds an attachment-picture to your Facebook message. If you specify a link, the image will function as a link.
     * If you set an attachment, you cannot set any additional links with setLink(...).
     *
     * @param string $name	This is the title of the attachment (top-most descriptive text)
     * @param string $caption	This is the image caption (text under the title)
     * @param string $uriAttachment	Full uri to the image you wish to attach. This location must be visible to Facebook, they will cache the picture.
     * @param string $uriLinkOut	Full uri to where you sish the image to link to
     * @param string $description	Description field in the message. If you do not specify this, Facebook may crawl the website specified in the link/image and attach a meta-description foiund from there to the message.
     */
    public function setAttachment($name, $caption, $uriAttachment, $uriLinkOut = null, $description = null)
    {
        $attachment = array(
            'name' => $name,
            'caption' => $caption,
            'picture' =>  $uriAttachment,
        );

        if (!empty($description)) {
            $attachment['description'] = $description;
        }
        if (!empty($uriLinkOut)) {
            $attachment['link'] = $uriLinkOut;
        }
        $this->attachment = $attachment;
    }

    /**
     * Shortcut for adding a link to you Facebook-message without an image. If you
     * specify an attachment with setAttachment(...), that will be used instead of this link.
     *
     * @param string $name This is the title of the link (top-most descriptive text)
     * @param string $linkOut Full uri to where you wish to link to
     * @param string $caption Optional description of the link
     */
    public function setLink($name, $linkOut, $caption = null)
    {
        $this->linkout = array(
            'name' => $name,
            'link' => $linkOut
        );
        if (!empty($caption)) {
            $this->linkout['caption'] = $caption;
        }
     }

    /**
     * Formats this object into on array that can be fed to the php-api class. This function will be called automatically.
     *
     * @throws \InvalidArgumentException;
     * @return array
     */
    public function formatData()
    {
        if (empty($this->accessToken)) {
            throw new \InvalidArgumentException('cannot format Facebook message: Facebook access token is empty');
        }
        if (empty($this->message)) {
            throw new \InvalidArgumentException('cannot format Facebook message: Message is empty, nothing to send');
        }
        $streamPost = array(
            'access_token' => $this->accessToken,
            'message' => $this->message
        );
        if (!empty($this->linkout)) {
            $streamPost = array_merge($streamPost, $this->linkout);
        }
        if (!empty($this->attachment)) {
            $streamPost = array_merge($streamPost, $this->attachment);
        }

        return $streamPost;
    }
}