<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
final class Custom_MailChimp_RBO_MergeField extends RBO_Recordset
{
    public static function contactFieldDisplay($record, $nolink = false, $desc = [])
    {
        /** @var array $fields */
        $fields = Custom_MailChimpCommon::get_fields('contact');

        return $fields[$record[$desc['id']]] ?? null;
    }

    public static function contactFieldQfField($form, $field, $label, $mode, $default, $desc, $rb_obj)
    {
        /* @var Utils_RecordBrowser $rb_obj */
        /* @var HTML_QuickForm $form */

        if (empty($rb_obj->record['list'] ?? null) === true) {
            $form->removeElement('list');
        }

        $form->freeze('list');

        if ($mode !== 'edit') {
            // Select
            $existingFields = (new self())->get_records(['list' => $rb_obj->record['list']]);
            $excludeFields = ['last_name', 'first_name', 'email'];

            foreach ($existingFields as $existingField) {
                $excludeFields[] = $existingField['contact_field'];
            }

            $fields = Custom_MailChimpCommon::get_fields('contact', $excludeFields);
            $form->addElement('select', $field, $label, $fields);
        }
    }

    public static function defaultQfField($form, $field, $label, $mode, $default, $desc, $rb_obj)
    {
        /* @var Utils_RecordBrowser $rb_obj */
        /* @var HTML_QuickForm $form */
        Utils_RecordBrowserCommon::QFfield_checkbox($form, $field, $label, $mode, $default, $desc, $rb_obj);

        $form->freeze($field);
    }

    public static function rpcMergeField(array $values, string $mode): array
    {
        if ($mode === 'added') {
            // TODO: sync to mailchimp.
        }

        if ($mode === 'add' || $mode === 'edit') {
            $values['tag'] = \strtoupper($values['tag']);
        }

        return $values;
    }

    public static function tagDisplay($record, $nolink = false, $desc = []): string
    {
        $custom = '<strong>*|' . $record[$desc['id']] . '|*</strong>';

        if (empty($record['merge_id']) === true) {
            return '<span style="display:inline-block;width:5px;"></span>' . $custom;
        }

        $default = '<strong>*|MERGE' . $record['merge_id'] . '|*</strong>';

        return '<span style="display:inline-block;width:5px;"></span>' . $custom . ' or ' . $default;
    }

    public static function typeQfField($form, $field, $label, $mode, $default, $desc, $rb_obj)
    {
        $types = [
            'text' => 'text',
            'number' => 'number',
            'address' => 'address',
            'phone' => 'phone',
            'date' => 'date',
            'url' => 'url',
            'imageurl' => 'imageurl',
            'radio' => 'radio',
            'dropdown' => 'dropdown',
            'birthday' => 'birthday',
            'zip' => 'zip'
        ];

        $form->addElement('select', $field, $label, $types);
    }

    /**
     * {@inheritDoc}
     */
    function fields()
    {
        $fields = [];

        $fields[] = (new RBO_Field_Select(_M('List'), (new Custom_MailChimp_RBO_List())->table_name(), ['name']));
        $fields[] = (new RBO_Field_Text(_M('Contact Field'), 32))
            ->set_QFfield_callback([self::class, 'contactFieldQfField'])
            ->set_display_callback([self::class, 'contactFieldDisplay'])
            ->set_visible();

        $fields[] = (new RBO_Field_Text(_M('Type'), '32'))
            ->set_QFfield_callback([self::class, 'typeQfField'])
            ->set_visible();
        $fields[] = (new RBO_Field_Integer(_M('Merge Id')))->set_extra();

        $fields[] = (new RBO_Field_Text(_M('Tag'), 10))
            ->set_display_callback([self::class, 'tagDisplay'])
            ->set_required()
            ->set_visible();

        $fields[] = (new RBO_Field_Checkbox(_M('Default')))
            ->set_QFfield_callback([self::class, 'defaultQfField'])
            ->set_extra();

        return $fields;
    }

    /**
     * @return RBO_Record[]
     */
    public function getDefaultFields(): array
    {
        return $this->get_records(['default' => 1]);
    }

    public function getForList($listId)
    {
        return $this->get_records(['list' => $listId]);
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        $success = parent::install();

        if ($success === true) {
            (new Custom_MailChimp_RBO_List())->new_addon(
                'Custom_MailChimp',
                'merge_field_addon',
                ['Custom_MailChimpCommon', 'merge_field_addon_label']
            );

            $this->register_processing_callback([self::class, 'rpcMergeField']);
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    function table_name()
    {
        return 'custom_mailchimp_list_merge_field';
    }

    public function uninstall()
    {
        $success = parent::install();

        if ($success === false) {
            return $success;
        }

        // Do post install.
        (new Custom_MailChimp_RBO_List())->delete_addon('Custom_MailChimp', 'merge_field_addon');

        return $success;
    }
}
