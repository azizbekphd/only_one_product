<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Settings;
use Tygh\Enum\VariationSources;

fn_register_hooks(
    'pre_add_to_cart',
    'pre_place_order',
    'pre_add_to_wishlist',
);

define(
    'VARIATION_SOURCE',
    VariationSources::getValue(
        Settings::instance()->getValue('variation_source', 'only_one_product'),
    )
);

