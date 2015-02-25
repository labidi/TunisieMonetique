<?php
class SaddemLabidi_TunisieMonetique_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code = 'tunisiemonetique';
    
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;
    
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('tunisiemonetique/payment/redirect', array('_secure' => false));
    }
}
?>