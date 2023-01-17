<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Enum\NotificationSeverity;

function fn_set_warning($msg)
{
    fn_set_notification(NotificationSeverity::WARNING, __('warning'), $msg, 'S');
}

function fn_get_product_ids($products)
{
    $product_ids = [];
    foreach ($products as $key => $data) {
        array_push($product_ids, $data['product_id']);
    }
    return $product_ids;
}

function fn_get_product_ids_without_variations($products)
{
    $product_ids = fn_get_product_ids($products);
    $check_variations = count(
        db_get_array(
            'SHOW COLUMNS FROM ?:products LIKE ?s',
            'parent_product_id')
    ) === 1;

    if (!$check_variations) {
        return $product_ids;
    }

    $variations = db_get_hash_single_array(
        'SELECT product_id, parent_product_id FROM ' .
        '?:products WHERE product_id IN (?n)',
        ['product_id', 'parent_product_id'], $product_ids
    );
    foreach ($product_ids as &$id) {
        if (isset($variations[$id])) {
            $id = $variations[$id];
        }
    }
    return $product_ids;
}

function fn_reset_amounts_to_one(&$product_data, $cart)
{
    $cart_products = fn_get_product_ids_without_variations($cart['products']);

    foreach ($product_data as $key => $data) {
        if (in_array($key, $cart_products)) {
            unset($product_data[$key]);
            fn_set_warning(__('only_one_product.already_in_cart'));
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
        fn_set_notification(__('only_one_product.anonymous_purchases_prohibited'));
        return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
    }
    $ids = fn_get_product_ids($order['products']);

    foreach ($ids as $id) {
        $link = [
            'user_id' => $user_id,
            'product_id' => $id,
        ];
        array_push($links, $link);
    }

    db_query('REPLACE INTO ?:only_one_product_user_links ?m', $links);
}

