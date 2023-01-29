<?php

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'view' || $mode === 'quick_view') {
    Tygh::$app['view']->assign('show_qty', false);
}
