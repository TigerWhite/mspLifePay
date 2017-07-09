<?php

class mspLifePay
{
    /** @var modX $modx */
    public $modx;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $this->config = array_merge(array(
            'api_url' => $this->modx->getOption('msplifepay_print_url', null,
                'https://sapi.life-pay.ru/cloud-print/create-receipt'
            ),
            'api_login' => $this->modx->getOption('msplifepay_api_login'),
            'api_key' => $this->modx->getOption('msplifepay_api_key'),
            'api_timeout' => $this->modx->getOption('msplifepay_api_timeout', null, 10),
            'test_mode' => $this->modx->getOption('msplifepay_test_mode', null, false),
            'print_mode' => $this->modx->getOption('msplifepay_print_mode', null, 'email'),
        ), $config);
    }


    /**
     * @param msOrder $order
     *
     * @return bool
     */
    public function sendData($order)
    {
        /** @var modUserProfile $profile */
        $profile = $order->getOne('UserProfile');

        $data = array(
            'ext_id' => (string)$order->get('id'),
            'apikey' => $this->config['api_key'],
            'login' => $this->config['api_login'],
            'test' => !empty($this->config['test_mode']),
            'mode' => $this->config['print_mode'],
            'type' => 'payment',
            'card_amount' => $order->get('cost'),
            'customer_email' => !empty($profile)
                ? $profile->email
                : '',
            /*
            'customer_phone' => !empty($profile)
                ? ($profile->mobilephone
                    ? $profile->mobilephone
                    : $profile->phone)
                : '',
            */
            'purchase' => array(
                'products' => array(),
            ),
        );
        $products = $order->getMany('Products');
        /** @var msOrderProduct $product */
        foreach ($products as $product) {
            $item = array(
                'name' => $product->name,
                //'price' => $product->price,
                'price' => $product->cost,
                'quantity' => $product->count,
            );
            $options = $product->get('options');
            if (isset($options['vat'])) {
                $item['vat'] = $options['vat'];
            }
            if (isset($options['unit'])) {
                $item['unit'] = $options['unit'];
            }
            /*
            if (!empty($options['discount'])) {
                if (strpos($options['discount'], '%') !== false) {
                    $item['discount'] = array(
                        'type' => 'percent',
                        'value' => trim($options['discount'], '%'),
                    );
                } else {
                    $item['discount'] = array(
                        'type' => 'amount',
                        'value' => $options['discount'],
                    );
                }
            }
            */
            $data['purchase']['products'][] = $item;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->config['api_url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->config['api_timeout']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $result = @json_decode(curl_exec($curl), true);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $log = "Request: " . print_r($data, true) . "\nResponse: " . print_r($result, true) .
            "\nInfo: " . print_r($info, true);
        if (!empty($result['code'])) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[mspLifePay] Error when trying to send data.\n{$log}");

            return false;
        } elseif (!empty($this->config['test_mode'])) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
            $this->modx->log(xPDO::LOG_LEVEL_INFO, "[mspLifePay] Test mode is enabled.\n{$log}");
            $this->modx->setLogLevel($level);
        }

        return true;
    }

}