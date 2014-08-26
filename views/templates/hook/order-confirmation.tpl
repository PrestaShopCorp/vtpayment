{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: VTPayment order confirmation page
**
** This template is displayed to the customer upon order creation
**
*}
{if $vtpayment_order.valid == 1}
<div class="conf confirmation">
	{l s='Congratulations! Your payment is done, and your order has been saved under' mod='vtpayment'}
	{if isset($vtpayment_order.reference)}
		{l s='the reference' mod='vtpayment'} <b>{$vtpayment_order.reference|escape:html:'UTF-8'}</b>
	{else}
		{l s='the ID' mod='vtpayment'} <b>{$vtpayment_order.id|escape:html:'UTF-8'}</b>
	{/if}.
</div>
{else}
<div class="error">
	{l s='Unfortunately, an error occurred during the transaction.' mod='vtpayment'}<br /><br />
	{if isset($vtpayment_order.reference)}
		({l s='Your Order\'s Reference:' mod='vtpayment'} <b>{$vtpayment_order.reference|escape:html:'UTF-8'}</b>)
	{else}
		({l s='Your Order\'s ID:' mod='vtpayment'} <b>{$vtpayment_order.id|escape:html:'UTF-8'}</b>)
	{/if}
</div>
{/if}
