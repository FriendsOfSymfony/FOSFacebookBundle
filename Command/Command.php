<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Command.
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
abstract class Command extends BaseCommand
{
    /**
     * get facebook sdk
     *
     * @codeCoverageIgnore
     *
     * @return \Facebook
     */
    public function getFacebook()
    {
        return $this->getContainer()->get('fos_facebook.api');
    }
}
