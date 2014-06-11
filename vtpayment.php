<?php
/**
 * This source file is subject to of the PHP license
 *
 * @author    VTPayment <support@vtpayment.com>
 * @copyright (c) 1997-2001 The PHP Group 
 * @license
 */

class vtpayment extends PaymentModule
{
	private $_html = '';
	private $post_errors = array();

	public function __construct()
	{
		$this->name = 'vtpayment';
		$this->displayName = 'VTPayment';
		$this->tab = 'payments_gateways';
		$this->version = 1.1;
		$this->author = 'VTPayment';
		$arr = array('PAYMENT_URL', 'MERCHANT_ID', 'PAY_TYPE', 'PAY_METHOD', 'LANGUAGE', 'INITIAL_ORDER_STATUS_ID', 'VTPAYMENT_SECURE_HASH_SECRET');
		$config = Configuration::getMultiple($arr);

		if (isset($config['PAYMENT_URL']))
			$this->payment_url = $config['PAYMENT_URL'];
		if (isset($config['MERCHANT_ID']))
			$this->merchant_id = $config['MERCHANT_ID'];
		if (isset($config['PAY_TYPE']))
			$this->pay_type = $config['PAY_TYPE'];
		if (isset($config['PAY_METHOD']))
			$this->pay_method = $config['PAY_METHOD'];
		if (isset($config['LANGUAGE']))
			$this->language = $config['LANGUAGE'];
		if (isset($config['INITIAL_ORDER_STATUS_ID']))
			$this->initial_order_status_id = $config['INITIAL_ORDER_STATUS_ID'];
		if ($config['VTPAYMENT_SECURE_HASH_SECRET'] == null)
			$this->secure_hash_secret = '';
		else
			$this->secure_hash_secret = $config['VTPAYMENT_SECURE_HASH_SECRET'];
		parent::__construct();
		//$this->page = basename(__FILE__, '.php');
		$this->description = 'vtpayment Online Payment Module.';

	}
	public function install()
	{
		//Call PaymentModule default install function
		return	parent::install()&&$this->registerHook('payment')&&$this->registerHook('paymentReturn');
                     //   return true;// added 0605
	}
	public function uninstall()
	{
            return  Configuration::deleteByName('PAYMENT_URL')&&
                    Configuration::deleteByName('MERCHANT_ID')&&
                    Configuration::deleteByName('PAY_TYPE')&&
                    Configuration::deleteByName('PAY_METHOD')&&
                    Configuration::deleteByName('LANGUAGE')&&
                    Configuration::deleteByName('INITIAL_ORDER_STATUS_ID')&&
                    Configuration::deleteByName('VTPAYMENT_SECURE_HASH_SECRET')&&
                    parent::uninstall();
               // return true;// added 0605
	}
	public function getContent()
	{
		if (!empty($_POST))
		{
			$this->postValidation();
			if (!count($this->post_errors))
				$this->postProcess();
			else
				foreach ($this->post_errors as $err)
					$this->_html .= "<div class='alert error'>{$err}</div>";
		}
		else
		$this->_html .= '<br />';
		$this->displaycheckout();
		$this->displayForm();
		return $this->_html;
        }


	public function execPayment($cart)
        
	{
		//$delivery = new Address(intval($cart->id_address_delivery));
	//	$invoice = new Address(intval($cart->id_address_invoice));
		$customer = new Customer($cart->id_customer);
		//global  $smarty;
		$products = $cart->getProducts();
		foreach ($products as $key => $product)
		{
		$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
		}
		$payment_url			= Configuration::get('PAYMENT_URL');
		$merchant_id			= Configuration::get('MERCHANT_ID');
	//	$pay_type			= Configuration::get('PAY_TYPE');
	//	$pay_method			= Configuration::get('PAY_METHOD');
	//	$language			= Configuration::get('LANGUAGE');
		$init_order_status_id	= Configuration::get('INITIAL_ORDER_STATUS_ID');
		$secure_hash_secret	= Configuration::get('VTPAYMENT_SECURE_HASH_SECRET');
		$amount			= number_format($cart->getOrderTotal(true, 3), 2, '.', '');
		$amount = sprintf('%.2f', $amount);
		$show_amount = $amount;
		$amount = $amount * 100;
		$amount = str_pad( $amount, 12, '0', STR_PAD_LEFT);
		//get currency code
		$curr = Currency::getCurrency($cart->id_currency);
		$currency = $curr['iso_code'];
		//$currency = $curr['iso_code_num'];
		date_default_timezone_set('PRC');
		$trans_time = date('YmdHis');
		/**
		 * Save the order record first with status e.g. Pending/Awaiting cilpay Payment 
		 **/
		$cart_id = $cart->id;
		$message = '';
		$this->validateOrder($cart_id, $init_order_status_id, $amount, $this->displayName, $message, array(), null, false, $customer->secure_key);

		//get order ID
		$order_id = Order::getOrderByCartId($cart_id);
		$order = new Order($order_id);

		$order_ref			= $cart_id.$order->reference;
	//	$remark				= $cart_id;
		/* to identify if using http or https */
//		if (!empty($_SERVER['HTTPS']))
//		{
//			if ($_SERVER['HTTPS'] !== 'off')
//				$http_orhttps = 'https';
//			else
//				$http_orhttps = 'http';
//		}
//		else
//		$http_orhttps = 'http';
                
                $http_orhttps = (!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] !== 'off') ? 'https': 'https';



		$success_url = $http_orhttps.'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/vtpayment/validation.php?success=true&cart_id='.$cart_id;
		$acq_id = '99020344';
		$array = array(
			'version'                   =>'VER000000001',
			'charSet'                   =>'UTF-8',
			'transType'                 =>'PURC',
			'orderNum'                  => $order_ref,
			'orderAmount'               => $amount,
			'orderCurrency'           	=> $currency,
			'merReserve'                =>'',
			'frontURL'                 => $success_url,
			'backURL'                  => $success_url,
			'merID'                     => $merchant_id,
			'acqID'                     =>$acq_id,
			'paymentSchema'             =>'UP',
			'transTime'                 =>$trans_time,
			'signType'                  =>'MD5',
			'cart_id'                  =>$cart_id
		);
                $secure_hash = '';
		if ($secure_hash_secret == '')
			$secure_hash = '';
		else
			$secure_hash	= $this->generatePaymentSecureHash($array);
			$this->smarty->assign( array(
			'paymentUrl' 		=> $payment_url,
			'version'                   =>'VER000000001',
			'charSet'                   =>'UTF-8',
			'transType'                 =>'PURC',
			'orderNum'                  => $order_ref,
			'orderAmount'               => $amount,
			'orderCurrency'             => $currency,
			'merReserve'                =>'',
			'frontURL'                  => $success_url,
			'backURL'                   => $success_url,
			'merID'                     => $merchant_id,
			'acqID'                     =>$acq_id,
			'paymentSchema'             =>'UP',
			'transTime'                 =>$trans_time,
			'signType'                  =>'MD5',
			'signature'                 =>$secure_hash,
			'cart_id'                   =>$cart_id,
			'showAmount'                =>$show_amount
	));

		return $this->display(__FILE__, 'payment_execution.tpl');
	}




	public function generatePaymentSecureHash($md5_array)
	{
		ksort($md5_array);
		$md5str = '';
                foreach ($md5_array as $k => $v)
			$md5str .= $k.'='.$v.'&';
		$md5str = Tools::substr($md5str, 0, Tools::strlen($md5str) - 1);
		//encrypted by md5
		return md5($md5str.Configuration::get('VTPAYMENT_SECURE_HASH_SECRET'));
	}
        
	public function verifyPaymentDatafeed($dataAry)
	{
                $buffer =$dataAry['src'].'|'.$dataAry['prc'].'|'.$dataAry['success_code'].'|'.$dataAry['merchant_reference_number'].'|'.
		$dataAry['vtpaymentdollar_reference_number'].'|'.$dataAry['currency_code'].'|'.$dataAry['amount'].'|'.$dataAry['payer_authentication_status'].'|'.$dataAry['secure_hash_secret'];
		$verify_data = sha1($buffer);
		if ($secure_hash == $verify_data)
			return true;
		return false;
	}
	public function hookPayment()
	{
		//global $smarty;
		$this->smarty->assign(array('this_path' => $this->_path));
		return $this->display(__FILE__, 'payment.tpl');
	}
	public function hookPaymentReturn($params)
	{
		//global $smarty;
		$state = $params['objOrder']->getCurrentState();
		if ($state == _PS_OS_OUTOFSTOCK_ || $state == _PS_OS_PAYMENT_)
			$this->smarty->assign(array(
				'total_to_pay' 	=> Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false, false),
				'status' 		=> 'ok',
				'id_order' 		=> $params['objOrder']->id
			));
		else
			$this->smarty->assign('status', 'failed');

		return $this->display(__FILE__, 'payment_return.tpl');
	}
	private function postValidation()
	{
            //   include '../../tools/profiling/Tools.php';
            if (Tools::getValue('btnSubmit'))
            {
            $payment_url = Tools::getValue('PAYMENT_URL');
            if ($payment_url === false)
            $this->post_errors[] = 'Payment URL is required.';
            $merchant_id = Tools::getValue('MERCHANT_ID');
            if ($merchant_id === false)
            $this->post_errors[] = 'Merchant ID is required.';
            $secure_hash_secret = Tools::getValue('VTPAYMENT_SECURE_HASH_SECRET');
            if ($secure_hash_secret === false)
            $this->post_errors[] = 'VTPAYMENT_SECURE_HASH_SECRET is required.';
		//if (empty($_POST['PAY_TYPE']))
		//	$this->_postErrors[] = 'Pay Type is required.';
		//if (empty($_POST['PAY_METHOD']))
		//	$this->_postErrors[] = 'Pay Method is required.';
		//if (empty($_POST['LANGUAGE']))
		//	$this->_postErrors[] = 'Language is required.';
		//if (empty($_POST['INITIAL_ORDER_STATUS_ID']))
		//$this->_postErrors[] = 'Initial Order Status ID is required.';
	}
	}

	private function postProcess()
	{
		if ((Tools::getValue('btnSubmit')))
		{
			$payment_url = Tools::getValue('PAYMENT_URL');
			$merchant_id = Tools::getValue('MERCHANT_ID');
			$pay_type = Tools::getValue('PAY_TYPE');
			$pay_method = Tools::getValue('PAY_METHOD');
			$language = Tools::getValue('LANGUAGE');
			$initial_order_status_id = Tools::getValue('INITIAL_ORDER_STATUS_ID');
			$secure_hash_secret = Tools::getValue('VTPAYMENT_SECURE_HASH_SECRET');
			Configuration::updateValue('PAYMENT_URL', $payment_url);
			Configuration::updateValue('MERCHANT_ID', $merchant_id);
			Configuration::updateValue('PAY_TYPE', $pay_type);
			Configuration::updateValue('PAY_METHOD', $pay_method);
			Configuration::updateValue('LANGUAGE', $language);
			Configuration::updateValue('INITIAL_ORDER_STATUS_ID', $initial_order_status_id);
			if (Tools::getValue('VTPAYMENT_SECURE_HASH_SECRET'))
				$secure_hash_secret = $secure_hash_secret;
			else
				$secure_hash_secret = '';
			Configuration::updateValue('VTPAYMENT_SECURE_HASH_SECRET', $secure_hash_secret);

			$updated = 'saved';
//			$this->_html .= "<div class='conf confirm'>{$updated}</div>";
                        $this->_html .= "<div  style='font-size: 12px;font-weight: normal;margin: 0 0 10px 0;line-height: 20px;padding: 13px 5px 5px 40px;min-height: 28px;background: #DFF2BF url(../img/admin/icon-valid.png) no-repeat scroll 8px 8px;border: 1px solid #4F8A10;color: #4F8A10;border-radius: 3px;'>{$updated}</div>";
		}
	}




	private function displaycheckout()
	{
		$modconfirm	= '';
		$modstatus = '';
		$this->_html .= "<link  media='screen' rel='stylesheet' href='../modules/vtpayment/css/vtcss3.css'><div class='wap'>";
		$this->_html .= "<header class='headerTop'><div class='vt3logo'><img src='../modules/vtpayment/img/vtLogo.png'></div>";
		$this->_html .= "<div class='slogan'><p>Sell to China,Paid via vtpayment Enabled by VTPayment</p></div><div class='header_btn'><ul><li>";
		$this->_html .= "<a class='start' href='https://www.vtpayment.com/regist/toRegist'>GET STARTED</a></li><li class='sign_btn'>";
		$this->_html .= "<a href='https://www.vtpayment.com/regist/toRegist'>Sign up now with VTPayment</a></li></ul></div></header>";
		$this->_html .= "<div class='vt3top'>In order to use UnionPay Online Payment service, please click";
		$this->_html .= "<a href='https://www.vtpayment.com/regist/toRegist'>get started</a> to register merchant infromation and";
		$this->_html .= "<a href='#EndConfiguration'>configure</a> certain parameters (Gateway URL, Merchant ID, Security Code) received in your register";
		$this->_html .= "mailbox after registration.</div><div class='vt3Con'><h3>A Worldwide Payment Platform Offers You</h3>";
		$this->_html .= '<div><dl><dt>International Business</dt><dd>We support merchants from North America, Asia Pacific';
		$this->_html .= "and European regions and provide 11+ transaction currencies for option.</dd></dl><dl class='paddingLfet'><dt>";
		$this->_html .= 'Easy Management</dt><dd>You could search daily business,void and refund transactions on merchant service';
		$this->_html .= 'platform directly.</dd></dl><dl><dt>2 Days Payout</dt><dd>If certain payment amount is reached, your payment';
		$this->_html .= "will be initiated in just 2 business days after transactions taken place.</dd></dl><dl  class='paddingLfet'>";
		$this->_html .= '<dt>Multiple channel schema option</dt><dd>We are connected with all Chinese major online payment channels';
		$this->_html .= 'like UnionPay, Alipay and more will come soon.</dd></dl><dl><dt>Secure and Stable</dt><dd>';
		$this->_html .= 'We build a state-of-art payment gateway following high industry standards to ensure the security';
		$this->_html .= "and stability of service.</dd></dl><dl  class='paddingLfet'><dt>Various Marketing Promotion for Free";
		$this->_html .= '</dt><dd>We give you support for marketing promotion and recommend you company to our cooperative partner';
		$this->_html .= 'actively. </dd></dl><dl><dt>Expand with UnionPay</dt><dd>There are more than 3.5 billion UnionPay cards can';
		$this->_html .= "be available if you choose to sign up with us.</dd></dl></div></div><div class='vt3Accept'><h3>Accept payments";
		$this->_html .= " worldwide using all the UnionPay credit & debit cards.</h3><ul><li><img src='../modules/vtpayment/img/unpay.png'>";
		$this->_html .= "</li><li class='vtSupportEmail'>Get payments in USD, EUR, HKD and JPY, and support 11+ transaction currencies.";
		$this->_html .= "<br>If you have any questions, please contact us via <a href=''>support@vtpayment.com</a>.</li></ul></div>";
		$this->_html .= "<div class='vt3End' name='EndConfiguration' id='EndConfiguration'><h3>Configuration</h3><a class='vt3Endtext'>";
		$this->_html .= 'In order to use this plug-in, please fill out the credentials provided by VTPayment.</a>';
		$this->_html .= "</div>{$modstatus}{$modconfirm}";
	}
	
	private function displayForm()
	{
		$modcilpay = 'VTPayment Configuration';
		$mod_client_label_payment_url = '* Gateway URL:';
		$mod_client_value_payment_url = $this->payment_url;
		$mod_client_label_merchant_id = '* Merchant ID:';
		$mod_client_value_merchant_id = $this->merchant_id;
		$mod_client_label_pay_type = '* Payment Type:';
		$mod_client_value_pay_type = $this->pay_type;
		$mod_client_label_pay_method = 'Pay Method';
		$mod_client_value_pay_method = $this->pay_method;
		$mod_client_label_language = 'Language';
		$mod_client_value_language = $this->language;
		$mod_client_label_initial_order_status_id = 'Initial Order Status ID:';
		// $mod_client_value_initial_order_status_id = $this->initial_order_status_id;
		$mod_client_label_secure_hash_secret = '* Security Code(MD5):';
		$mod_client_value_secure_hash_secret = $this->secure_hash_secret;
		$mod_update_settings = 'save';
		$this->_html .= "<div class='vt3Plug'><p id='a'><form action='{$_SERVER['REQUEST_URI']}' method='post'>";
		$this->_html .= "<fieldset style='width: 960px;text-align: center;margin: 0px auto; '>";
		$this->_html .= "<legend class='vtnew'><img src='../img/admin/access.png' />{$modcilpay}</legend>";
		$this->_html .= "<table style='border:0;width:800px;cellpadding:2;cellspacing:0;overflow:hidden;margin:0 auto;text-align:center;'id='form'>";
		$this->_html .= "<tr><td height='30'></td></tr><tr><td width='300' class='vttd' style='text-align: right;color:#000'>";
		$this->_html .= "{$mod_client_label_payment_url}</td><td class='vttd' style='text-align: left; padding-left:20px;' width='400'>";
		$this->_html .= "<input type='text' name='PAYMENT_URL' value='{$mod_client_value_payment_url}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr><td width='300'  class='vttd' style='text-align: right;color:#000'>{$mod_client_label_merchant_id}</td>";
		$this->_html .= "<td class='vttd' style='text-align:left;padding-left:20px;color:#000'>";
		$this->_html .= "<input type='text' name='MERCHANT_ID' value='{$mod_client_value_merchant_id}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr><td width='300'  class='vttd' style='text-align: right;color:#000'>{$mod_client_label_secure_hash_secret}</td>";
		$this->_html .= "<td class='vttd'  style='text-align: left; padding-left:20px;color:#000' >";
		$this->_html .= "<input type='text' name='VTPAYMENT_SECURE_HASH_SECRET' value='{$mod_client_value_secure_hash_secret}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr  style='display:none'><td class='vttd'>{$mod_client_label_pay_type}</td><td class='vttd'>";
		$this->_html .= "<input type='text' name='PAY_TYPE' value='{$mod_client_value_pay_type}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr style='display:none'><td class='vttd'>{$mod_client_label_pay_method}</td><td>";
		$this->_html .= "<input type='text' name='PAY_METHOD' value='{$mod_client_value_pay_method}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr  style='display:none'><td>{$mod_client_label_language}</td><td>";
		$this->_html .= "<input type='text' name='LANGUAGE' value='{$mod_client_value_language}' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr><td width='300'  class='vttd'  style='text-align: right;color:#000' >{$mod_client_label_initial_order_status_id}</td>";
		$this->_html .= "<td class='vttd'  style='text-align: left; padding-left:20px;' >";
		$this->_html .= "<input type='text' name='INITIAL_ORDER_STATUS_ID' value='1' style='width: 300px;' /></td></tr>";
		$this->_html .= "<tr><td width='960' colspan='2' align='center' class='vttd' color='#000'>";
		$this->_html .= "<input class='button' name='btnSubmit' value='{$mod_update_settings}' type='submit' /></td></tr></table></fieldset>";
		$this->_html .= '</form></p></div></div>';
	}
}
