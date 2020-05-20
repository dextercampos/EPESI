<?php
// Simply load the files if it does not yet exist in case it's already required in composer.

defined('_VALID_ACCESS') || die('Direct access forbidden');

if (\class_exists(\DrewM\MailChimp\MailChimp::class) === false) {
    require_once 'modules/Custom/MailChimp/libs/vendor/drewm/mailchimp-api/MailChimp.php';
}

if (\class_exists(\DrewM\MailChimp\Webhook::class) === false) {
    require_once 'modules/Custom/MailChimp/libs/vendor/drewm/mailchimp-api/Webhook.php';
}
