<?php

if (!class_exists('msPaymentInterface')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(__FILE__))) . '/minishop2/model/minishop2/mspaymenthandler.class.php';
}

class LifePay extends msPaymentHandler implements msPaymentInterface
{
    /**
     * LifePay constructor.
     *
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);
    }


    /**
     * @param msOrder $order
     *
     * @return array|string
     */
    public function send(msOrder $order)
    {
        if ($order->get('status') > 1) {
            return $this->error('ms2_err_status_wrong');
        }
        $http_query = $this->getPaymentLink($order);

        return $this->success('', array('redirect' => $http_query));
    }


    /**
     * @param msOrder $order
     *
     * @return string
     */
    public function getPaymentLink(msOrder $order)
    {
        return $this->modx->getOption('site_url') . '?order_id=' . $order->id;
    }


    /**
     * @param msOrder $order
     * @param int $status
     *
     * @return bool
     */
    public function receive(msOrder $order, $status = 0)
    {
        return false;
    }

}