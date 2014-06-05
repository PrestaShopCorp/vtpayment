{*
 * This source file is subject to of the PHP license
 *
 * @author    VTPayment <support@vtpayment.com>
 * @copyright (c) 1997-2001 The PHP Group 
 * @license
 *}
 
{capture name=path}UnionPay{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>Order Summary</h2>

{if isset($nbProducts) && $nbProducts <= 0}
    <p class="warning">Your shopping cart is empty.</p>
{else}

<h3>your OrderNum:{$orderNum|escape:'htmlall':'UTF-8'}</h3>
<h3>your OrderAmount:{$showAmount|escape:'htmlall':'UTF-8'}</h3>
<form name="checkout_confirmation" action="{$paymentUrl|escape:'htmlall':'UTF-8'}" method="post" />

<input type="hidden" name="version" value="VER000000001">
<input type="hidden" name="charSet" value="UTF-8">
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
    <p>&nbsp;</p>
    	
    <p>
          UnionPay  Payment.
        <br /><br />
        <b>Please Confirmation your  Order.</b>
    </p>
    <p class="cart_navigation">
     <a href="javascript:document.checkout_confirmation.submit();" class="exclusive_large">Confirmation Order </a>
       
        <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="button_large">Other Payment Method</a>
    </p>
    
</form>

{/if}