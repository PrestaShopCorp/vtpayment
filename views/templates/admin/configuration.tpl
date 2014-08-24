{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
** @version  Release: $Revision: 1.2.0 $
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: VTPayment addon's configuration page
*}
<link  media='screen' rel='stylesheet' href="{$module_dir|escape:'htmlall':'UTF-8'}css/vtcss3.css">
<div class='wap'>
	<header class='headerTop'>
		<div class='vt3logo'><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/vtLogo.png"></div>
		<div class='slogan'><p>{l s='Sell to China with VTPayment' mod='vtpayment'}</p></div>
		<div class='header_btn'>
			<ul>
				<li>
					<a class='start' href='https://www.vtpayment.com/regist/toRegist'>{l s='GET STARTED' mod='vtpayment'}</a>
				</li>
			</ul>
		</div>
	</header>
	<div class='vt3top'>
		{l s='In order to use UnionPay Online Payment service, please click get started to register merchant infromation and configure certain parameters (Gateway URL, Merchant ID, Security Code) received in your register mailbox after registration.' mod='vtpayment'}
	</div>
	<div class='vt3Con'><h3>{l s='A Worldwide Payment Platform Offers You' mod='vtpayment'}</h3>
		<div>
			<dl>
				<dt>{l s='International Business' mod='vtpayment'}</dt>
				<dd>{l s='We support merchants from North America, Asia Pacificand European regions and provide 11+ transaction currencies for option.' mod='vtpayment'}</dd>
			</dl>
			<dl class='paddingLfet'>
				<dt>{l s='Easy Management' mod='vtpayment'}</dt>
				<dd>{l s='You could search daily business,void and refund transactions on merchant service platform directly.' mod='vtpayment'}</dd>
			</dl>
			<dl>
				<dt>{l s='2 Days Payout' mod='vtpayment'}</dt>
				<dd>{l s='If certain payment amount is reached, your payment will be initiated in just 2 business days after transactions taken place.' mod='vtpayment'}</dd>
			</dl>
			<dl  class='paddingLfet'>
				<dt>{l s='Multiple channel schema option' mod='vtpayment'}</dt>
				<dd>{l s='We are connected with all Chinese major online payment channels like UnionPay, Alipay and more will come soon.' mod='vtpayment'}</dd>
			</dl>
			<dl>
				<dt>{l s='Secure and Stable' mod='vtpayment'}</dt>
				<dd>{l s='We build a state-of-art payment gateway following high industry standards to ensure the security and stability of service.' mod='vtpayment'}</dd>
			</dl>
			<dl  class='paddingLfet'>
				<dt>{l s='Various Marketing Promotion for Free' mod='vtpayment'}</dt>
				<dd>{l s='We give you support for marketing promotion and recommend you company to our cooperative partner actively.' mod='vtpayment'}</dd>
			</dl>
			<dl>
				<dt>{l s='Expand with UnionPay' mod='vtpayment'}</dt>
				<dd>{l s='There are more than 3.5 billion UnionPay cards can be available if you choose to sign up with us.' mod='vtpayment'}</dd>
			</dl>
		</div>
	</div>
	<div class='vt3Accept'>
		<h3>{l s='Accept payments worldwide using all the UnionPay credit & debit cards.' mod='vtpayment'}</h3>
		<ul>
			<li><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/unpay.png"></li>
			<li class='vtSupportEmail'>
				{l s='Get payments in USD, EUR, HKD and JPY, and support 11+ transaction currencies.' mod='vtpayment'}
				<br>
				{l s='If you have any questions, please contact us via ' mod='vtpayment'}
				<a href=''>{l s='support@vtpayment.com' mod='vtpayment'}</a>.
			</li>
		</ul>
	</div>
	<div class="vt3End">
		{if $vtpayment_validation}
				{foreach from=$vtpayment_validation item=validation}
					{$validation|strval}<br />
				{/foreach}
		{/if}
		{if $vtpayment_error}
				{foreach from=$vtpayment_error item=error}
					{$error|strval}<br />
				{/foreach}
		{/if}
		{if $vtpayment_warning}
				{foreach from=$vtpayment_warning item=warning}
					{$warning|strval}<br />
				{/foreach}
		{/if}
	</div>
	<br />
	<div class='vt3Plug'>
		<form action="{$vtpayment_form_link|escape:'htmlall':'UTF-8'}" method="post" id="vtpayment_api_settings" class="half-form L">
			<fieldset>
				<legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/settings.gif" alt="" /><span>{l s='VTPayment API Settings' mod='vtpayment'}</span></legend>
				<div id="vtpayment-basic-settings-table">
					<label for="vtpayment_gateway_url">{l s='VTPayment Gateway URL:' mod='vtpayment'}</label>
					<div class="margin-form">
						<input type="text" name="vtpayment_gateway_url" class="input-text" value="{if $vtpayment_configuration.VTPAYMENT_GATEWAY_URL}{$vtpayment_configuration.VTPAYMENT_GATEWAY_URL|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
					</div>
					<label for="vtpayment_merchant_id">{l s='VTPayment Merchant ID:' mod='vtpayment'}</label>
					<div class="margin-form">
						<input type="text" name="vtpayment_merchant_id" class="input-text" value="{if $vtpayment_configuration.VTPAYMENT_MERCHANT_ID}{$vtpayment_configuration.VTPAYMENT_MERCHANT_ID|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
					</div>
					<label for="vtpayment_secret_key">{l s='VTPayment Secret Key:' mod='vtpayment'}</label>
					<div class="margin-form">
						<input type="text" name="vtpayment_secret_key" class="input-text" value="{if $vtpayment_configuration.VTPAYMENT_SECRET_KEY}{$vtpayment_configuration.VTPAYMENT_SECRET_KEY|escape:'htmlall':'UTF-8'}{/if}" /> <sup>*</sup>
					</div>
				</div>
				<div class="margin-form">
					<input type="submit" name="SubmitBasicSettings" class="button" value="{l s='Save settings' mod='vtpayment'}" />
				</div>
				<span class="small"><sup style="color: red;">*</sup> {l s='Required fields' mod='vtpayment'}</span>
			</fieldset>
		</form>
	</div>
</div>
