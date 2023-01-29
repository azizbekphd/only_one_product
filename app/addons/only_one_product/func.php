<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\OrderStatuses;
use Tygh\Enum\VariationSources;

function fn_set_warning($msg)
{
    fn_set_notification(NotificationSeverity::WARNING, __('warning'), $msg, 'S');
}

function fn_get_products_ids($products)
{
    $product_ids = [];
    foreach ($products as $key => $data) {
        array_push($product_ids, $data['product_id']);
    }
    return $product_ids;
}

function fn_get_product_variations($product_id)
{
    $variations = [];
    if (VariationSources::isParentProductId(VARIATION_SOURCE)) {
        $variations = db_get_fields(
            'SELECT product_id FROM ?:products WHERE ' .
            'parent_product_id = ?i', $product_id
        );
        $is_parent = count($variations) > 0;
        if (!$is_parent) {
            $parent_id = db_get_field(
                'SELECT parent_product_id FROM ' .
                '?:products WHERE product_id = ?i',
                $product_id
            );
            if (!empty($parent_id)) {
                $variations = fn_get_product_variations($parent_id);
            }
        }
    } elseif (VariationSources::isVariationGroup(VARIATION_SOURCE)) {
        $group_id = db_get_field(
            'SELECT group_id FROM ?:product_variation_group_products ' .
            'WHERE product_id = ?i', $product_id
        );
        if (!empty($group_id)) {
            $variations = db_get_fields(
                'SELECT product_id FROM ' .
                '?:product_variation_group_products WHERE ' .
                'group_id = ?i', $group_id
            );
        }
    }

    $variations[] = $product_id;
    return $variations;
}

function fn_product_is_already_in_cart($product_variations, $cart)
{
    return count(array_intersect(
        $product_variations,
        fn_get_products_ids($cart['products']),
    )) > 0;
}

function fn_product_is_already_bought($product_variations, $auth)
{
    $user_id = $auth['user_id'];
    $exclude_statuses = [
        OrderStatuses::CANCELED,
    ];
    $orders = db_get_array(
        'SELECT ?:orders.order_id FROM ?:orders ' .
        'INNER JOIN ?:order_details ' .
        'ON ?:orders.order_id = ?:order_details.order_id ' .
        'AND ?:orders.user_id = ?i ' .
        'AND ?:orders.status NOT IN (?a) ' .
        'AND ?:order_details.product_id IN (?n)',
        $user_id, $exclude_statuses, $product_variations);
    return !empty($orders);
}

function fn_only_one_product_pre_add_to_cart(&$product_data, $cart, $auth, $update)
{
    foreach ($product_data as $key => $data) {
        $product_id = $data['product_id'];
        $product_variations = fn_get_product_variations($product_id);
        if (fn_product_is_already_in_cart($product_variations, $cart)) {
            unset($product_data[$key]);
            fn_set_warning(__('only_one_product.already_in_cart'));
        } else if (fn_product_is_already_bought($product_variations, $auth)) {
            unset($product_data[$key]);
            fn_set_warning(__('only_one_product.already_bought'));
        } else {
            $product_data[$key]['amount'] = 1;
        }
    }
}

function fn_only_one_product_place_order(&$order_id, &$action, &$order_status)
{
    $order_info = fn_get_order_info($order_id);
    if (empty($order_info['user_id'])) {
        fn_set_warning(__('only_one_product.anonymous_purchases_prohibited'));
        return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
    }
}

