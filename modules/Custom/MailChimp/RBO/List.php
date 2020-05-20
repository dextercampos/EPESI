<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
final class Custom_MailChimp_RBO_List extends RBO_Recordset
{
    public static function mailChimpIdDisplay($record, $nolink = false, $desc = [])
    {
        return $record[$desc['id']] ?? null;
    }

    public static function mailChimpIdQfField(
        $form,
        $field,
        $label,
        $mode,
        $default,
        $desc,
        $rb_obj
    ): void {
        if ($mode === 'add') {
            return;
        }
        /** @var \HTML_QuickForm $form */
        Utils_RecordBrowserCommon::QFfield_text($form, $field, $label, $mode, $default, $desc, $rb_obj);
        if ($mode === 'edit') {
            $form->freeze($field);
        }
    }

    public static function rpcList(array $values, string $mode): array
    {
        $mergeFieldRbo = new Custom_MailChimp_RBO_MergeField();
        if ($mode === 'added' || $mode === 'restored') {
            // POC: new lists are added to mailchimp through the cron.

            // Add default merge fields
            $defaultMergeFields = $mergeFieldRbo->getDefaultFields();
            foreach ($defaultMergeFields as $mergeField) {
                $newMergeField = $mergeField->clone_data();
                $newMergeField->default = 0;
                $newMergeField->list = $values['id'];
                $newMergeField->save();
            }

            return $values;
        }

        if ($mode === 'deleted') {
            $mergeFieldRbo = new Custom_MailChimp_RBO_MergeField();
            $mergeFields = $mergeFieldRbo->get_records(['list' => $values['id']]);

            foreach ($mergeFields as $mergeField) {
                $mergeField->delete();
            }

            Custom_MailChimp_Bridge_List::listDelete($values['mailchimp_id']);

            return $values;
        }

        if ($mode === 'edited') {
            Custom_MailChimp_Bridge_List::listUpdate($values['mailchimp_id'], $values);
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    function fields()
    {
        $fields = [];

        $fields[] = (new RBO_Field_Text(_M('Name'), 255))->set_required()->set_visible();

        $fields[] = (new RBO_Field_LongText(_M('Permission Reminder')))->set_required();
        $fields[] = (new RBO_Field_Checkbox(_M('Email Type Option')));
        $fields[] = (new RBO_Field_Calculated(_M('Linked'), 'checkbox'))->set_extra();

        // List Contact
        $fields[] = (new RBO_Field_PageSplit(_M('Contact')));
        $fields[] = (new RBO_Field_Text(_M('Company Name'), 255))->set_required();
        $fields[] = (new RBO_Field_Text(_M('Address 1'), 255))->set_required();
        $fields[] = (new RBO_Field_Text(_M('Address 2'), 255));
        $fields[] = (new RBO_Field_Text(_M('City'), 100))->set_required();
        $fields[] = (new RBO_Field_Text(_M('State'), 100));
        $fields[] = (new RBO_Field_Text(_M('Postal Code'), 10))->set_required();

        $fields[] = (new RBO_Field_CommonData(_M('Country'), 'Countries'))
            ->set_QFfield_callback(['Data_CountriesCommon', 'QFfield_country'])->set_required();

        $fields[] = (new RBO_Field_Text(_M('Phone'), 20));

        $fields[] = (new RBO_Field_PageSplit(_M('Campaign Defaults')));
        $fields[] = (new RBO_Field_Text(_M('From Name'), 255))->set_required();
        $fields[] = (new CRM_Contacts_RBO_Email(_M('From Email')))->set_required();
        $fields[] = (new RBO_Field_Text(_M('Subject'), 255))->set_required();

        $fields[] = (new RBO_Field_Calculated(_M('MailChimp ID'), 'text', 64))
            ->set_display_callback([self::class, 'mailChimpIdDisplay'])
            ->set_QFfield_callback([self::class, 'mailChimpIdQfField'])
            ->set_extra();

        return $fields;
    }

    public function install()
    {
        $success = parent::install();
        if ($success === false) {
            return $success;
        }

        // Do post install.
        $this->set_caption(_M('MailChimp Lists'));
        $this->register_processing_callback([self::class, 'rpcList']);

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function table_name()
    {
        return 'custom_mailchimp_list';
    }
}
