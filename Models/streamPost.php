<?php
/*
 *	This file is part of FOSFacebookBundle.
 *
 *	(c) Teemu Reibacka <teemu.reisbacka@gmail.com>
 *
 *	This class functions as a model for messages to be sent by the facebookPush-service to the Facebook api.
 */

namespace FOS\FacebookBundle\Models;

class streamPost
{
    protected $accessToken;
    protected $message;
    protected $attachment;

    /**
     * Sets the access token used in message body. This is set automatically inside facebookPush-service.
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    /**
     *
     * Sets the actual text content of the message
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Adds an attachment-picture to your Facebook message. If you specify a link, the image will function as a link.
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
     * Shortcut for adding a link to you FAcebook-message without an image. The setAttachment(...) will override this function.
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
        if (!empty($caption)) $this->linkout['caption'] = $caption;
    }
    /**
     * Formats this object into on array that can be fed to the php-api class. This function will be called automatically.
     * @throws \Exception
     * @return array
     */
    public function formatData()
    {
        if (empty($this->accessToken)) {
            throw new \Exception('cannot format Facebook message: Facebook access token is empty');
        }
        if (empty($this->message)) {
            throw new \Exception('cannot format Facebook message: Message is empty, nothing to send');
        }
        $stream_post = array(
            'access_token' => $this->accessToken,
            'message' => $this->message
        );
        if (!empty($this->linkout)) {
            $stream_post = array_merge($stream_post, $this->linkout);
        }
        if (!empty($this->attachment)) {
            $stream_post = array_merge($stream_post, $this->attachment);
        }

        return $stream_post;
    }
}
?>