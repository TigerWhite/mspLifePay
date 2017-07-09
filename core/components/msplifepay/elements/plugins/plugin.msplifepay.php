<?php
/** @var modX $modx */
switch ($modx->event->name) {
    case 'msOnChangeOrderStatus':
        /** @var mspLifePay $mspLifePay */
        $mspLifePay = $modx->getService('mspLifePay', 'mspLifePay', MODX_CORE_PATH . 'components/msplifepay/model/');
        /** @var int $status */
        /** @var msOrder $order */
        if ($mspLifePay && $status == 2 && !$order->get('type')) {
            /** @var msPayment $payment */
            if ($payment = $order->getOne('Payment')) {
                // Do not process payments with empty handler
                if ($payment->get('class')) {
                    $mspLifePay->sendData($order);
                }
            }
        }
        break;
}