<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\Tests\Security\Authentication\Token;

use FOS\FacebookBundle\Security\Authentication\Token\FacebookUserToken;

class FacebookUserTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testThatAlwaysReturnEmptyCredentials($uid, $roles)
    {
        $token = new FacebookUserToken('main', $uid, $roles);

        $this->assertEquals('', $token->getCredentials());
    }

    /**
     * @return array
     */
    public static function provider()
    {
        return array(
            array('', array()),
            array('l3l0', array()),
            array('', array('role1', 'role2')),
            array('l3l0', array('role1', 'role2'))
        );
    }

    public function testThatProviderKeyIsNotEmptyAfterDeserialization()
    {
        $providerKey = 'main';
        $token = unserialize(serialize(new FacebookUserToken($providerKey)));

        $this->assertEquals($providerKey, $token->getProviderKey());
    }
}
