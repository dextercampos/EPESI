<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
final class Custom_MailChimp_RBO_Member extends RBO_Recordset
{

    public static function contactDisplay()
    {
        // For now do not display anything.
    }

    public static function contactQfField()
    {
        // For now do not display anything.
    }

    public static function emailTypeQfField($form, $field, $label, $mode, $default, $desc, $rb_obj)
    {
        $types = [
            'text' => 'text',
            'html' => 'html'
        ];

        $form->addElement('select', $field, $label, $types);
    }

    public static function statusQfField($form, $field, $label, $mode, $default, $desc, $rb_obj)
    {
        $status = [
            'subscribed' => 'subscribed',
            'unsubscribed' => 'unsubscribed',
            'cleaned' => 'cleaned',
            'pending' => 'pending',
            'transactional' => 'transactional'
        ];

        $form->addElement('select', $field, $label, $status);
    }

    /**
     * @inheritDoc
     */
    function fields()
    {
        $fields = [];

        $fields[] = (new CRM_Contacts_RBO_Email(_M('Email')))->set_required()->set_visible();

        // TODO: common data ('html' or 'text').
        $fields[] = (new RBO_Field_Text(_M('Email Type'), 4))
            ->set_QFfield_callback([self::class, 'emailTypeQfField']);

        // List Contact
        $fields[] = (new RBO_Field_Text(_M('Status'), 20))
            ->set_required()
            ->set_QFfield_callback([self::class, 'statusQfField']);
        $fields[] = (new RBO_Field_Text(_M('Subscriber Hash'), 255));
        $fields[] = (new RBO_Field_Text(_M('Unsubscribe Reason'), 255));

        $fields[] = (new RBO_Field_Calculated(_M('Contact'), 'integer'))
            ->set_display_callback([self::class, 'contactDisplay'])
            ->set_QFfield_callback([self::class, 'contactQfField']);

        $fields[] = (new RBO_Field_Select(_M('List'), (new Custom_MailChimp_RBO_List())->table_name(),
            ['name']))->set_visible();

        return $fields;
    }

    public function install()
    {
        $success = parent::install();
        if ($success === false) {
            return $success;
        }

        // Do post install.
        $this->set_caption(_M('MailChimp Members'));

        Utils_RecordBrowserCommon::new_addon(
            'contact',
            'Custom_MailChimp',
            'contact_addon',
            ['Custom_MailChimpCommon', 'contact_addon_label']
        );

        return $success;
    }

    /**
     * @inheritDoc
     */
    function table_name()
    {
        return 'custom_mailchimp_list_member';
    }
}
