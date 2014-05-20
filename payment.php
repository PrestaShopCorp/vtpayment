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

$union_pay = new UnionPay();

echo $union_pay->execPayment(Context::getContext()->cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>
