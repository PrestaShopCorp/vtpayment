<?php

/*
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2014 PrestaShop SA
 *
 */
if (!defined('_PS_VERSION_'))
	exit;

/* Backward compatibility */
if (version_compare(_PS_VERSION_, '1.5', '<'))
	require_once (_PS_MODULE_DIR_.'vtpayment/backward_compatibility/backward.php');

class VTPayment extends PaymentModule
{

	private $_error = array();
	private $_validation = array();
	private $md5_len = 32;

	public function __construct()
	{
		$this->name = 'vtpayment';
		$this->version = '1.0.0';
		$this->author = 'VTPayment';
		$this->className = 'VTPayment';
		$this->tab = 'payments_gateways';

		parent::__construct();

		$this->displayName = $this->l('VTPayment');
		$this->description = $this->l('Accept payments using VTPayment.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
	}

	public function install()
	{
		return parent::install() && $this->registerHook('payment');
	}

	/**
	 * VTPayment uninstallation process:
	 *
	 * Step 1 - Remove Configuration option values from database
	 * Step 2 - Uninstallation of the Addon itself
	 *
	 * @return boolean Uninstallation result
	 */
	public function uninstall()
	{
		$keys_to_uninstall = array('VTPAYMENT_GATEWAY_URL', 'VTPAYMENT_MERCHANT_ID', 'VTPAYMENT_SECRET_KEY');

		$result = true;
		foreach ($keys_to_uninstall as $key_to_uninstall)
			$result &= Configuration::deleteByName($key_to_uninstall);

		return $result && parent::uninstall();
	}

	/* VTPayment configuration section
	 *
	 * @return HTML page (template) to configure the Addon
	 */

	public function getContent()
	{
		/* Update the Configuration option values depending on which form has been submitted */
		if (Tools::isSubmit('SubmitBasicSettings'))
			$this->_saveSettingsBasic();

		/* VTPayment API credentials should be filled */
		if (Configuration::get('VTPAYMENT_GATEWAY_URL') == '' ||
				Configuration::get('VTPAYMENT_MERCHANT_ID') == '' || Configuration::get('VTPAYMENT_SECRET_KEY') == '')
			$this->_warning[] = $this->l('In order to use VTPayment, please provide your API credentials.');

		// add 1.4 token support
		if(method_exists('Tools','getAdminTokenLite')){
			$token = Tools::getAdminTokenLite('AdminModules');
		}else{
			$tabid = (int)Tab::getCurrentTabId();
			$employee_id = (int)$this->context->cookie->id_employee;
			$token = 'AdminModules'.$tabid.$employee_id;
			$token = Tools::getAdminToken($token);
		}
		$this->context->smarty->assign(array(
			'vtpayment_form_link' => './index.php?tab=AdminModules&configure=vtpayment&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module='.$this->tab.'&module_name=vtpayment',
			'vtpayment_validation' => (empty($this->_validation) ? false : $this->_validation),
			'vtpayment_error' => (empty($this->_error) ? false : $this->_error),
			'vtpayment_warning' => (empty($this->_warning) ? false : $this->_warning),
			'vtpayment_configuration' => Configuration::getMultiple(array('VTPAYMENT_GATEWAY_URL', 'VTPAYMENT_MERCHANT_ID', 'VTPAYMENT_SECRET_KEY')),
		));

		return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
	}

	/*
	 * VTPayment configuration section - Basic settings (API credentials options)
	 */

	private function _saveSettingsBasic()
	{
		if (!isset($_POST['vtpayment_gateway_url']) || !$_POST['vtpayment_gateway_url'] || !isset($_POST['vtpayment_merchant_id']) || !$_POST['vtpayment_merchant_id'] || !isset($_POST['vtpayment_secret_key']) || !$_POST['vtpayment_secret_key'])
			$this->_error[] = $this->l('Please fill in all required fields.');

		Configuration::updateValue('VTPAYMENT_GATEWAY_URL', pSQL(Tools::getValue('vtpayment_gateway_url')));
		Configuration::updateValue('VTPAYMENT_MERCHANT_ID', pSQL(Tools::getValue('vtpayment_merchant_id')));
		Configuration::updateValue('VTPAYMENT_SECRET_KEY', pSQL(Tools::getValue('vtpayment_secret_key')));

		/* TODO: Automated check to verify the API credentials configured by the merchant */

		if (!count($this->_error))
			$this->_validation[] = $this->l('Congratulations, your configuration was updated successfully');
	}

	/* VTPayment payment hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookPayment() method (Order details, etc.)
	 *
	 * @return HTML content (Template) displaying the VTPayment payment methods
	 */

	public function hookPayment($params)
	{
		$html = '';

		/* Display a form/button that will be sent to VTPayment with the customer details */

		/* Fixed parameters */
		$version = 'VER000000001';
		$charset = 'UTF-8';
		$trans_type = 'PURC';
		$mer_reserve = '';
		$acq_id = '99020344';
		$payment_schema = 'UP';
		$sign_type = 'MD5';

		$trans_time = date('YmdHis');

		/* Order related parameters */
		$cart_id = (int)$this->context->cart->id;
		$order_num = $this->_generateOrderNumber($cart_id, $trans_time);
		$order_amount = number_format($this->context->cart->getOrderTotal(true, 3), 2, '.', '');
		$order_amount = sprintf('%.2f', $order_amount);
		$order_amount = $order_amount * 100;
		$order_amount = str_pad( $order_amount, 12, '0', STR_PAD_LEFT);
		$order_currency = $this->context->currency->iso_code;

		/* Configuration parameters */
		$gateway_url = Configuration::get('VTPAYMENT_GATEWAY_URL');
		$merchant_id = Configuration::get('VTPAYMENT_MERCHANT_ID');
		$secret_key = Configuration::get('VTPAYMENT_SECRET_KEY');

		$front_url = Tools::getShopDomainSsl(true)._MODULE_DIR_.'vtpayment/validation.php';
		$back_url = $front_url;

		$array = array(
			'version' => $version,
			'charSet' => $charset,
			'transType' => $trans_type,
			'orderNum' => $order_num,
			'orderAmount' => $order_amount,
			'orderCurrency' => $order_currency,
			'merReserve' => $mer_reserve,
			'frontURL' => $front_url,
			'backURL' => $back_url,
			'merID' => $merchant_id,
			'acqID' => $acq_id,
			'paymentSchema' => $payment_schema,
			'transTime' => $trans_time,
			'signType' => $sign_type,
			'cart_id' => $cart_id,
		);

		$signature = $this->_generateSignature($array, $secret_key);

		$this->context->smarty->assign($array + array('signature' => $signature, 'action' => $gateway_url));

		$html .= $this->display(__FILE__, 'views/templates/hook/payment.tpl');

		return $html;
	}

	private function _generateSignature($array, $secret)
	{
		ksort($array);
		$str = '';
		foreach ($array as $k => $v)
			$str .= $k.'='.$v.'&';
		$str = substr($str, 0, strlen($str) - 1);
		return md5($str.$secret);
	}

	private function _generateOrderNumber($cart_id, $trans_time)
	{
		$secret = Configuration::get('VTPAYMENT_SECRET_KEY');
		return md5($cart_id.$trans_time.$secret).$cart_id;
	}

	public function validateOrderNumber($order_no, $trans_time)
	{
		$secret = Configuration::get('VTPAYMENT_SECRET_KEY');
		$cart_id = $this->getCartId($order_no);
		$signature = substr($order_no, 0, $this->md5_len);


		if (md5($cart_id.$trans_time.$secret) == $signature)
			return true;
		else
			return false;
	}

	public function getCartId($order_no)
	{
		return substr($order_no, $this->md5_len, strlen($order_no) - $this->md5_len);
	}
}
