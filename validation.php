<?php
/**
 * This source file is subject to of the PHP license
 *
 * @author    VTPayment <support@vtpayment.com>
 * @copyright (c) 1997-2001 The PHP Group 
 * @license
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/vtpayment.php');

$cart_id = Tools::getValue('cart_id');
$code = Tools::getValue('RespCode');
$order_id = Order::getOrderByCartId($cart_id);
$order = new Order($order_id);
//$union_pay = new vtpayment();

if (Tools::getValue('success') && $code == '00')
{
	$order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
	header('Location: '.__PS_BASE_URI__.'modules/vtpayment/success.php?cart_id='.$cart_id);
}
