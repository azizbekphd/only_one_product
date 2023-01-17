<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode === 'checkout') {
    if (empty($auth['user_id'])) {
        $url = 'auth.login_form?return_url=checkout.checkout';

        return [CONTROLLER_STATUS_REDIRECT, $url];
    }
}

