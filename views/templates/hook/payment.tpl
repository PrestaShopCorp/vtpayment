{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2015 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: VTPayment payment form template
**
** This template is displayed on the payment page and called by the Payment hook
*}
<form name="checkout_confirmation" action="{$action|escape:'htmlall':'UTF-8'}" method="post">
	<p class="payment_module">
		<input type="hidden" name="version" value="{$version|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="charSet" value="{$charSet|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="transType" value="{$transType|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderNum" value="{$orderNum|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderAmount" value="{$orderAmount|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderCurrency" value="{$orderCurrency|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="merReserve" value="{$merReserve|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="frontURL" value="{$frontURL|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="backURL" value="{$backURL|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="merID" value="{$merID|escape:'htmlall':'UTF-8'}">
		<input type="hidden" name="acqID" value="{$acqID|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="paymentSchema" value="{$paymentSchema|escape:'htmlall':'UTF-8'}"/>
		<input type="hidden" name="transTime" value="{$transTime|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="signType" value="{$signType|escape:'htmlall':'UTF-8'}"/>
		<input type="hidden" name="signature" value="{$signature|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="cart_id" value="{$cart_id|escape:'htmlall':'UTF-8'}" />
		<a href="javascript:document.checkout_confirmation.submit();" title="{l s='Pay with VTPayment' mod='vtpayment'}">
			<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/unpay.png" alt="{l s='Pay with VTPayment' mod='vtpayment'}"/>
			&nbsp;{l s='Pay with VTPayment' mod='vtpayment'}
		</a>
	</p>
</form>
