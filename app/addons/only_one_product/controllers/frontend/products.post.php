<?php

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'view' || $mode === 'quick_view') {
    $product_id = $_REQUEST['product_id'];
    $single_copy = fn_is_allowed_only_single_copy($product_id);
    Tygh::$app['view']->assign('show_qty', !$single_copy);
}
