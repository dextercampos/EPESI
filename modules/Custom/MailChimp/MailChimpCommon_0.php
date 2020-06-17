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

    public static function help()
    {
        return Base_HelpCommon::retrieve_help_from_file(self::Instance()->get_type());
    }

    public static function menu(): array
    {
        if (Utils_RecordBrowserCommon::get_access((new Custom_MailChimp_RBO_List())->table_name(), 'browse')) {
            return [_M('CRM') => ['__submenu__' => 1, _M('MailChimp List') => []]];
        }

        return [];
    }

    public static function processWebhook()
    {
        Custom_MailChimp_Bridge_Webhook::applySubscriptions();
    }

    public static function syncNewLists(): ?string
    {
        $newLists = (new Custom_MailChimp_RBO_List())->get_records(['linked' => null]);
        $messages = [];
        foreach ($newLists as $newList) {
            try {
                $listData = $newList->to_array();
                if ($listData['mailchimp_id']) {
                    Custom_MailChimp_Bridge_List::listLinkExisting($listData);
                    continue;
                }
                Custom_MailChimp_Bridge_List::listCreate($listData);
            } catch (Exception $exception) {
                $messages[] = $exception->getMessage();
            }
        }

        return implode('<br/>', $messages);
    }
}

require_once 'modules/Custom/MailChimp/libs/autoload.php';
