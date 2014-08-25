<?php
/*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*
*  International Registered Trademark & Property of PrestaShop SA
*
*  Description: VTPayment validation controller
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/vtpayment.php');

$vtpayment = new VTPayment();

if($vtpayment->active) {
	$order_num = Tools::getValue('orderNum');
	$resp_code = Tools::getValue('RespCode');
	$trans_time = Tools::getValue('transTime');

	if ($resp_code == '00' && $vtpayment->validateOrderNumber($order_num, $trans_time)) {
		$context = Context::getContext();
		$cart_id = $vtpayment->getCartId($order_num);
		$cart = new Cart((int)$cart_id);
		$context->cart = $cart;
		if (Validate::isLoadedObject($cart)) {
			if ($cart->OrderExists()) {
				Tools::redirect('index.php?controller=history');
			}
			else {
				$customer = new Customer((int)$cart->id_customer);
				$context->customer = $customer;

				$currency = $context->currency;
				$total = (float)number_format($cart->getOrderTotal(true, 3), 2, '.', '');

				$order_status = (int)Configuration::get('PS_OS_PAYMENT');
				$vtpayment->validateOrder((int)$cart->id, (int)$order_status, (float)$total, $vtpayment->displayName, NULL, array(), null, false, $customer->secure_key);
				Tools::redirectLink(__PS_BASE_URI__.'history.php');
			}
		}
		else {
			Tools::redirect('index.php?controller=order&step=1');
		}
	}
	else {
		Tools::redirect('index.php?controller=order&step=1');
	}
}
else {
		Tools::redirect('index.php?controller=order&step=1');
}
