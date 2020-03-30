<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
class Custom_MailChimpCommon extends ModuleCommon
{
    use Custom_MailChimp_Traits_AdminCommon;
    use Custom_MailChimp_Traits_MergeFieldCommon;

    public static function contact_addon_label()
    {
        return ['show' => true, 'label' => __('MailChimp Member')];
    }

    public static function cron(): array
    {
        return ['syncNewLists' => 1];
    }

    public static function menu(): array
    {
        if (Utils_RecordBrowserCommon::get_access((new Custom_MailChimp_RBO_List())->table_name(), 'browse')) {
            return [_M('CRM') => ['__submenu__' => 1, _M('MailChimp List') => []]];
        }

        return [];
    }

    public static function syncNewLists(): ?string
    {
        try {
            $newLists = (new Custom_MailChimp_RBO_List())->get_records(['mailchimp_id' => '', 'linked' => null]);
            foreach ($newLists as $newList) {
                Custom_MailChimp_Bridge_List::listCreate($newList->to_array());
                // TODO: sync merge tags also
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

        return null;
    }

    public static function processWebhook()
    {
        Custom_MailChimp_Bridge_Webhook::applySubscriptions();
    }

}

require_once 'modules/Custom/MailChimp/libs/autoload.php';
