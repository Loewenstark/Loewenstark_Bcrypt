<?php

require_once 'Loewenstark/Bcrypt/Model/Customer/Customer/Trait.php';

class Loewenstark_Bcrypt_Model_Customer_Customer
extends Mage_Customer_Model_Customer
{
    use Loewenstark_Bcrypt_Model_Customer_Customer_Trait;
}