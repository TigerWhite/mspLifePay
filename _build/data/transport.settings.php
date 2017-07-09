<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();

$tmp = array(
    'api_login' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'msplifepay_main',
    ),
    'api_key' => array(
        'xtype' => 'text-password',
        'value' => '',
        'area' => 'msplifepay_main',
    ),
    'test_mode' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'msplifepay_main',
    ),
    /*
    'print_mode' => array(
        'xtype' => 'textfield',
        'value' => 'email',
        'area' => 'msplifepay_main',
    ),*/
);

foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'msplifepay_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
