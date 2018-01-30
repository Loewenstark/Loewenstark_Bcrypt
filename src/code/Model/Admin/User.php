<?php

require_once 'Loewenstark/Bcrypt/Model/Admin/User/Trait.php';

class Loewenstark_Bcrypt_Model_Admin_User
extends Mage_Admin_Model_User
{
    use Loewenstark_Bcrypt_Model_Admin_User_Trait;
}
