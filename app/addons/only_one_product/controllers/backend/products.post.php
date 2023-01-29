<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

if ($mode === 'update') {

    Registry::set('navigation.tabs.only_one_product', [
        'title' => __('only_one_product'),
        'js' => true,
    ]);



} elseif ($mode === 'add') {

    Registry::set('navigation.tabs.only_one_product', [
        'title' => __('only_one_product'),
        'js' => true,
    ]);

}
