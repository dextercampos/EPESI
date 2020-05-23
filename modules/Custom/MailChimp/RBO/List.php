<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
final class Custom_MailChimp_RBO_List extends RBO_Recordset
{
    public static function internalField()
    {
        // Field used internally within code only. No need for display.
    }

    public static function mailChimpIdDisplay($record, $nolink = false, $desc = [])
    {
        $value = $record[$desc['id']] ?? null;
        if ($value === null) {
            return null;
        }

        if ($nolink === true) {
            return $value;
        }

        $webId = $record['web_id'] ?? null;
        if (empty($webId)) {
            return sprintf(
                '%s - <i>not yet synced%s</i>',
                $value,
                Base_AclCommon::i_am_admin() ? ', make sure cron is configured and running.' : ''
            );
        }

        return sprintf(
            '<a href="%s/lists/dashboard/overview?id=%s" target="_blank">%s</a>',
            Custom_MailChimp_Bridge_List::getAdminEndpoint(),
            $record['web_id'],
            $value
        );
    }

    public static function mailChimpIdQfField($form, $field, $label, $mode, $default, $desc, $rb_obj): void
    {
        $label .= sprintf(
            ' <a href="%s" target="_blank"><img src="%s"/></a>',
            'https://mailchimp.com/help/find-audience-id/',
            Base_ThemeCommon::get_template_file(Base_MainModuleIndicator::module_name(), 'help.png')
        );
        /** @var \HTML_QuickForm $form */
        Utils_RecordBrowserCommon::QFfield_text($form, $field, $label, $mode, $default, $desc, $rb_obj);
        /** @var \Utils_RecordBrowser $rb_obj */
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

        $fields[] = (new RBO_Field_LongText(_M('Permission Reminder')))
            ->set_help('<strong>Remind people how they signed up to your audience</strong>' .
                '<p>Sometimes people forget they signed up for an email newsletter. ' .
                'To prevent false spam reports, it’s best to briefly remind your recipients how they got in your audience.</p>')
            ->set_required();

        $fields[] = (new RBO_Field_Checkbox(_M('Email Type Option')))
            ->set_help('<strong>Let subscribers pick plain-text or HTML emails</strong>' .
                '<p>Whether the list supports multiple formats for emails. ' .
                'When set to true, subscribers can choose whether they want to receive HTML or plain-text emails. ' .
                'When set to false, subscribers will receive HTML emails, with a plain-text alternative backup.</p>');

        $fields[] = (new RBO_Field_Text(_M('MailChimp ID'), 64))
            ->set_display_callback([self::class, 'mailChimpIdDisplay'])
            ->set_QFfield_callback([self::class, 'mailChimpIdQfField'])
            ->set_help('<strong>MailChimp Unique Audience ID</strong>' .
                '<p>Fill value if you want to link existing MailChimp list.</p>' .
                '<p>Leave empty if you want to create new list in MailChimp.</p>')
            ->set_visible();

        $fields[] = (new RBO_Field_Calculated(_M('Web ID'), 'text', 64))
            ->set_display_callback([self::class, 'internalField'])
            ->set_QFfield_callback([self::class, 'internalField']);

        $fields[] = (new RBO_Field_Calculated(_M('Linked'), 'checkbox'))
            ->set_display_callback([self::class, 'internalField'])
            ->set_QFfield_callback([self::class, 'internalField']);

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
        $this->set_icon(Base_ThemeCommon::get_template_filename(Custom_MailChimpInstall::module_name(), 'icon.png'));

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
