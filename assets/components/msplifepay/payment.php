<?php

if (!isset($modx)) {
    define('MODX_API_MODE', true);
    require dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

    $modx->getService('error', 'error.modError');
}

$modx->error->message = null;
/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->loadCustomClasses('payment');
if (!class_exists('LifePay')) {
    exit('Error: could not load payment class "LifePay".');
}

/** @var msOrder $order */
$order = $modx->newObject('msOrder');
/** @var msPaymentInterface|LifePay $handler */
$handler = new LifePay($order);
//$response = $handler->send($order);