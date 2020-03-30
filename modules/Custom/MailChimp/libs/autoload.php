<?php
// Simply load the files if it does not yet exist in case it's already required in composer.

defined('_VALID_ACCESS') || die('Direct access forbidden');

require_once 'modules/Custom/MailChimp/libs/vendor/autoload.php';

\spl_autoload_register(function ($className) {
    // Register Dex/MailChimp namespace manually.

    if (\strpos($className, 'Dex\\MailChimp\\') === false) {
        return;
    }

    $className = \str_replace('Dex\\MailChimp\\', '', $className);
    $path = \sprintf('%s.php', __DIR__ . DIRECTORY_SEPARATOR . \str_replace('\\', DIRECTORY_SEPARATOR, $className));

    if (\file_exists($path) === false) {
        return;
    }

    require_once $path;
});
