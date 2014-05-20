{*
 * This source file is subject to of the PHP license
 *
 * @author    VTPayment <support@vtpayment.com>
 * @copyright (c) 1997-2001 The PHP Group 
 * @license
 *}
 
{if $status == 'ok'}
	<p>Your order has been completed. We are preparing to send its product immediately.
		<br /><br />For any questions or for further information, please contact our <a href="{$base_dir|escape:'htmlall':'UTF-8'}contact-form.php">customer support</a>.
	</p>
{else}
	<p class="warning">
		We noticed a problem with your order. If you think this is an error, you can contact our
		<a href="{$base_dir|escape:'htmlall':'UTF-8'}contact-form.php">customer support</a>.
	</p>
{/if}
