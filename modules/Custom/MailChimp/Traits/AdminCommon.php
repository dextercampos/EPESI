<?php
declare(strict_types=1);

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
trait Custom_MailChimp_Traits_AdminCommon
{
    public static function admin_caption(): array
    {
        return [
            'label' => __('MailChimp'),
            'section' => __('Features Configuration')
        ];
    }

    public static function admin_icon(): string
    {
        return (string)Base_ThemeCommon::get_template_file(Custom_MailChimp::module_name(), 'logo.svg');
    }
}
