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
include(dirname(__FILE__).'/unionpay.php');

$cart_id = $_REQUEST['cart_id'];

print ("
<br/>
<br/>
<table width='300' height='100' border='1'>
<tr>
<td><img src='http://www.red-php.com/images/green.gif'></td>
<td><h4>Paid Successfully</h4></td>
</tr>
</table>
");

?>
