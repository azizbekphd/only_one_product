<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_only_one_product_pre_add_to_cart(&$product_data, &$cart, &$auth, &$update)
{
    foreach ($product_data as $key => $data) {
        $product_data[$key]["amount"] = 1;
    }
}

