<?php

class Loewenstark_Bcrypt_Customer_AccountController
extends Mage_Core_Controller_Front_Action
{

    protected $_realModuleName = 'Mage_Customer';

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();
            $customer->setOldEmail($customer->getEmail());
            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                if (!$customer->validatePassword($this->getRequest()->getPost('current_password'))) {
                    $errors[] = $this->__('Invalid current password');
                }

                // If email change was requested then set flag
                $isChangeEmail = ($customer->getOldEmail() != $customer->getEmail()) ? true : false;
                $customer->setIsChangeEmail($isChangeEmail);

                // If password change was requested then add it to common validation scheme
                $customer->setIsChangePassword($this->getRequest()->getParam('change_password'));

                if ($customer->getIsChangePassword()) {
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    if (strlen($newPass)) {
                        /**
                         * Set entered password and its confirmation - they
                         * will be validated later to match each other and be of right length
                         */
                        $customer->setPassword($newPass);
                        $customer->setPasswordConfirmation($confPass);
                    } else {
                        $errors[] = $this->__('New password field cannot be empty.');
                    }
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                if (method_exists($customer, 'cleanPasswordsValidationData'))
                {
                    $customer->cleanPasswordsValidationData();
                    $customer->setData('confirmation', null);
                } else {
                    $customer->setData('password', null);
                    $customer->setData('password_confirmation', null);
                    $customer->setData('confirmation', null);
                }
                $customer->setPasswordCreatedAt(time());

                // Reset all password reset tokens if all data was sufficient and correct on email change
                if ($customer->getIsChangeEmail()) {
                    $customer->setRpToken(null);
                    $customer->setRpTokenCreatedAt(null);
                }

                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

                if (method_exists($customer, 'sendChangedPasswordOrEmail'))
                {
                    if ($customer->getIsChangeEmail() || $customer->getIsChangePassword()) {
                        $customer->sendChangedPasswordOrEmail();
                    }
                }

                $this->_redirect('customer/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/edit');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get model by path
     *
     * @param string $path
     * @param array|null $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }
}
