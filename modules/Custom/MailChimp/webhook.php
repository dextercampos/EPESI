<?php

$apiKey = $_REQUEST['token'] ?? null;
if ($_REQUEST['token'] === null) {
    die('Access forbidden!');
}

header('Access-Control-Allow-Origin: *');
define('_VALID_ACCESS', 1);
define('CID', false);
define('READ_ONLY_SESSION', true);

// epesi include
require_once '../../../include.php';

ModuleManager::load_modules();

if ($_REQUEST['token'] !== Custom_ApiCommon::getApiKey()) {
    die('Access forbidden!');
}
// Login as admin for now.
Acl::set_user(1);

Custom_MailChimpCommon::processWebhook();
