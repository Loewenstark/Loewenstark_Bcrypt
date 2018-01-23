<?php

class Loewenstark_Bcrypt_Model_Admin_Model_User
extends Mage_Admin_Model_User
{
    /**
     * Retrieve encoded password
     *
     * @param string $password
     * @return string
     */
    protected function _getEncodedPassword($password)
    {
        return $this->_getHelper('ldsbcrypt')
                ->getHash($password, self::HASH_SALT_LENGTH);
    }
}
