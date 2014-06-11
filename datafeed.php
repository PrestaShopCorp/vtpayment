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
include(dirname(__FILE__).'vtpayment.php');

$success_code 		= Tools::getIsset('successcode') 	? Tools::getIsset('successcode') 	: '';
$prc 				= Tools::getIsset('prc') 			? Tools::getIsset('prc')  			: '';
$src 				= Tools::getIsset('src') 			? Tools::getIsset('src')  			: '';
$order_ref 			= Tools::getIsset('Ref') 			?Tools::getIsset('Ref')  			: '';
$payment_ref 		= Tools::getIsset('PayRef') 		? Tools::getIsset('PayRef')		: '';
$currency	 		= Tools::getIsset('Cur') 			?Tools::getIsset('Cur')			: '';
$amount 			= Tools::getIsset('Amt') 			? Tools::getIsset('Amt') 			: '';
$payer_auth			= Tools::getIsset('payerAuth') 	? Tools::getIsset('payerAuth') 	: '';
$secure_hash 		= Tools::getIsset('secureHash') 	? Tools::getIsset('secureHash') 	: '';
$cart_id 			= Tools::getIsset('remark') 		? Tools::getIsset('remark')		: '';

echo 'OK! ';

$secure_hash_secret = Configuration::get('VTPAYMENT_SECURE_HASH_SECRET');

$order_id = Order::getOrderByCartId($cart_id);

if ($order_id)
{
	$order = new Order($order_id);
	$vtpayment = new vtpayment();
	if ($secure_hash_secret != ''){
                $dataAry =  array("src","prc","success_code","order_ref","payment_ref","currency","amount","payer_auth","secure_hash_secret","secure_hash");
		$is_valid_secure_hash = $vtpayment->verifyPaymentDatafeed($dataAry);
        }
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
