<?php

namespace Tygh\Enum;

/**
 * VariationSources contains possible values for variation list sources
 *
 * @package Tygh\Enum
 */
class VariationSources
{
    const PARENT_PRODUCT_ID = 'P';
    const VARIATION_GROUP = 'G';

    private const VARIANTS = [
        'parent_product_id' => self::PARENT_PRODUCT_ID,
        'variation_group' => self::VARIATION_GROUP,
    ];

    public static function getValue($source_id)
    {
        if (!array_key_exists($source_id, self::VARIANTS)) {
            return false;
        }

        return self::VARIANTS[$source_id];
    }

    public static function isParentProductId($source)
    {
        return $source === self::PARENT_PRODUCT_ID;
    }

    public static function isVariationGroup($source)
    {
        return $source === self::VARIATION_GROUP;
    }
}

