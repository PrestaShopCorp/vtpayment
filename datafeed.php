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


$success_code 		= isset($_REQUEST['successcode']) 	? $_REQUEST['successcode'] 	: '';
$prc 				= isset($_REQUEST['prc']) 			? $_REQUEST['prc'] 			: '';
$src 				= isset($_REQUEST['prc']) 			? $_REQUEST['src'] 			: '';
$order_ref 			= isset($_REQUEST['Ref']) 			? $_REQUEST['Ref'] 			: '';
$payment_ref 		= isset($_REQUEST['PayRef']) 		? $_REQUEST['PayRef']		: '';
$currency	 		= isset($_REQUEST['Cur']) 			? $_REQUEST['Cur'] 			: '';
$amount 			= isset($_REQUEST['Amt']) 			? $_REQUEST['Amt'] 			: '';
$payer_auth			= isset($_REQUEST['payerAuth']) 	? $_REQUEST['payerAuth'] 	: '';
$secure_hash 		= isset($_REQUEST['secureHash']) 	? $_REQUEST['secureHash']	: '';
$cart_id 			= isset($_REQUEST['remark']) 		? $_REQUEST['remark']		: '';

echo 'OK! ';

$secure_hash_secret	= Configuration::get('SECURE_HASH_SECRET');

$order_id = Order::getOrderByCartId($cart_id);

if ($order_id)
{
	$order = new Order($order_id);
	$unionpay = new unionpay();
	if ($secure_hash_secret != '')
		$is_valid_secure_hash = $unionpay->verifyPaymentDatafeed
		($src, $prc, $success_code, $order_ref, $payment_ref, $currency, $amount, $payer_auth, $secure_hash_secret, $secure_hash);
	if ($secure_hash_secret == '' || $is_valid_secure_hash)
	{
		if ($success_code == '0')
		{
			$order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
			echo ' - Accepted';
		}
	else
	{
			$order->setCurrentState(Configuration::get('PS_OS_ERROR'));
			echo ' - Rejected';
	}

	}
	else
	{
		echo ' - Invalid SecureHash';

	}

}
?>