<?php

class Loewenstark_Bcrypt_Model_Customer_Customer
extends Mage_Customer_Model_Customer
{
    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return $this->_getHelper('ldsbcrypt')
            ->getHash(trim($password), !is_null($salt) ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }
}