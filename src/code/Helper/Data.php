<?php

class Loewenstark_Bcrypt_Helper_Data
extends Mage_Core_Helper_Data
{
    /**
     * Generate salted hash from password
     *
     * @param string $password
     * @param string|integer|boolean $salt
     */
    public function getHash($password, $salt = false)
    {
        return $this->getEncryptor()
                ->getStrongHash($password, $salt);
    }

    /**
     * @return Loewenstark_Crypt_Model_Encryption
     */
    public function getEncryptor()
    {
        return parent::getEncryptor();
    }
}
