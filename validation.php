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

	$request = array(
		'GWTime' => Tools::getValue('GWTime'),
		'RespCode' => Tools::getValue('RespCode'),
		'RespMsg' => Tools::getValue('RespMsg'),
		'rate' => Tools::getValue('rate'),
		'settAmount' => Tools::getValue('settAmount'),
		'settCurrency' => Tools::getValue('settCurrency'),
		'transID' => Tools::getValue('transID'),
		'version' => Tools::getValue('version'),
		'charSet' => Tools::getValue('charSet'),
		'transType' => Tools::getValue('transType'),
		'orderNum' => Tools::getValue('orderNum'),
		'orderAmount' => Tools::getValue('orderAmount'),
		'orderCurrency' => Tools::getValue('orderCurrency'),
		'merReserve' => Tools::getValue('merReserve'),
		'merID' => Tools::getValue('merID'),
		'acqID' => Tools::getValue('acqID'),
		'paymentSchema' => Tools::getValue('paymentSchema'),
		'transTime' => Tools::getValue('transTime'),
		'signType' => Tools::getValue('signType'),
	);

	$signature = Tools::getValue('signature');

	if(!$vtpayment->verifySignature($request, $signature))
		die($vtpayment->l('Invalid Signature.'));

	if ($resp_code == '00' && $vtpayment->validateOrderNumber($order_num, $trans_time)) {
		$context = Context::getContext();
		$cart_id = $vtpayment->getCartId($order_num);
		$cart = new Cart((int)$cart_id);
		$context->cart = $cart;
		if (Validate::isLoadedObject($cart)) {
			$customer = new Customer((int)$cart->id_customer);
			$context->customer = $customer;

			if ($cart->OrderExists()) {
				Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)$context->cart->id.'&id_module='.(int)$vtpayment->id.'&id_order='.(int)$vtpayment->currentOrder.'&key='.$customer->secure_key);
			}
			else {
				$currency = $context->currency;
				$total = (float)number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', '');

				$order_status = (int)Configuration::get('PS_OS_PAYMENT');
				$vtpayment->validateOrder((int)$cart->id, (int)$order_status, (float)$total, $vtpayment->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);
				Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)$context->cart->id.'&id_module='.(int)$vtpayment->id.'&id_order='.(int)$vtpayment->currentOrder.'&key='.$customer->secure_key);
			}
		}
		else {
			die($vtpayment->l('Invalid Cart ID.'));
		}
	}
	else {
		die($vtpayment->l('Invalid order, please contact our Customer service.'));
	}
}
else {
	die($vtpayment->l('VTPayment module is not active.'));
}
