<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Enum\NotificationSeverity;

function fn_reset_amounts_to_one(&$product_data, $cart)
{
    $cart_products = fn_get_products_ids($cart['products']);

    foreach ($product_data as $key => $data) {
        if (in_array($key, $cart_products)) {
            unset($product_data[$key]);
            fn_set_notification(NotificationSeverity::WARNING, __('warning'), __('only_one_product.already_in_cart'), 'S');
        } else {
            $product_data[$key]['amount'] = 1;
        }
    }
}

function fn_only_one_product_pre_add_to_cart(&$product_data, $cart, $auth, $update)
{
    fn_reset_amounts_to_one($product_data, $cart);
}

function fn_only_one_product_place_order(&$order_id, &$action, &$order_status)
{
    $links = [];
    $order = fn_get_order_info($order_id);
    if (!empty($order['user_id'])) {
        $user_id = $order['user_id'];
    } else {
        fn_set_notification(NotificiatonSeverity::WARNING, __('warning'), __('only_one_product.anonymous_purchases_prohibited'), 'S');
        return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
    }
    $ids = fn_get_products_ids($order['products']);

    foreach ($ids as $id) {
        $link = [
            'user_id' => $user_id,
            'product_id' => $id,
        ];
        array_push($links, $link);
    }

    db_query('REPLACE INTO ?:only_one_product_user_links ?m', $links);
}

function fn_get_products_ids($products)
{
    $cart_products = [];
    foreach ($products as $key => $data) {
        array_push($cart_products, $data['product_id']);
    }
    return $cart_products;
}

