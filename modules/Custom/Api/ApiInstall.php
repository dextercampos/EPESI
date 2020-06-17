<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
class Custom_ApiInstall extends ModuleInstall
{
    public static function simple_setup(): array
    {
        return [
            'icon' => true,
            'package' => __('API Module'),
            'url' => 'http://mailchimp.com/'
        ];
    }

    public function info(): array
    {
        return [
            'Author' => '<a href="mailto:dexterjohncampos@gmail.com">Dexter John R. Campos</a>',
            'Description' => __('API Module')
        ];
    }

    public function install(): bool
    {
        return true;
    }

    public function requires($v): array
    {
        return [];
    }

    public function uninstall(): bool
    {
        return true;
    }

    public function version(): array
    {
        return ['0.1'];
    }
}
