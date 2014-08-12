{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: VTPayment payment form template
**
** This template is displayed on the payment page and called by the Payment hook
*}
<form name="checkout_confirmation" action="{$action|escape:'htmlall':'UTF-8'}" method="post">
	<p class="payment_module">
		<input type="hidden" name="version" value="VER000000001"/>
		<input type="hidden" name="charSet" value="UTF-8"/>
		<input type="hidden" name="transType" value="{$transType|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderNum" value="{$orderNum|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderAmount" value="{$orderAmount|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="orderCurrency" value="{$orderCurrency|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="merReserve" value="" />
		<input type="hidden" name="frontURL" value="{$frontURL|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="backURL" value="{$backURL|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="merID" value="{$merID|escape:'htmlall':'UTF-8'}">
		<input type="hidden" name="acqID" value="{$acqID|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="paymentSchema" value="UP" />
		<input type="hidden" name="transTime" value="{$transTime|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="signType" value="MD5" />
		<input type="hidden" name="signature" value="{$signature|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="cart_id" value="{$cart_id|escape:'htmlall':'UTF-8'}" />
		<a href="javascript:document.checkout_confirmation.submit();">
		<img src="https://online.unionpay.com/static/portal/images/global/logo.gif" alt="" style="vertical-align: middle; margin-right: 10px;"/></a> {l s='Pay with VTPayment' mod='vtpayment'}
	</p>
</form>
