<?php
/*
Mygateway Payment Controller
By: Junaid Bhura
www.junaidbhura.com
*/

class SaddemLabidi_TunisieMonetique_PaymentController extends Mage_Core_Controller_Front_Action {
	// The redirect action is triggered when someone places an order
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','tunisiemonetique',array('template' => 'tunisiemonetique/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}
	
	// The response action is triggered when your gateway sends back a response after processing the customer's payment
	public function notificationAction() {

		$orderid = $this->getRequest()->getParam('Reference')  ;
		$action = $this->getRequest()->getParam('Action')  ;
		$Param = $this->getRequest()->getParam('Param')  ;

		switch ($action) {
			case 'DETAIL':
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($orderid);

				echo "Reference=".$order->getId(). "&Action=".$action."&Reponse=".$orderid->getBaseGrandTotal();; 
				break;
			
			case 'ACCORD':
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($orderid);
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
				$order->sendNewOrderEmail();
				$order->setEmailSent(true);
				Mage::getSingleton('checkout/session')->unsQuoteId();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
				break;
			
			case 'REFUS':
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
				break;
			
			case 'ERREUR':
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
				break;
			
			
			case 'ANNULATION':
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
				break;
			
			default:
				die('sss') ;
				Mage_Core_Controller_Varien_Action::_redirect('/', array('_secure'=>true));
				break;
		}
	}
	
	// The cancel action is triggered when an order is to be cancelled
	public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				// Flag the order as 'cancelled' and save it
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
			}
        }
	}
}