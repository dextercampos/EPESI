<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
class Custom_MailChimpInstall extends ModuleInstall
{

    public static function simple_setup(): array
    {
        return [
            'icon' => true,
            'package' => __('MailChimp Integration'),
            'url' => 'http://mailchimp.com/'
        ];
    }

    public function info(): array
    {
        return [
            'Author' => '<a href="mailto:dexterjohncampos@gmail.com">Dexter John R. Campos</a>',
            'Description' => __('MailChimp Integration')
        ];
    }

    public function install(): bool
    {
        try {
            Base_ThemeCommon::install_default_theme($this->get_type());

            $list = new Custom_MailChimp_RBO_List();

            if ($list->install() === true) {
                $this->apply_default_permissions($list);
            }

            $member = new Custom_MailChimp_RBO_Member();

            if ($member->install() === true) {
                $this->apply_default_permissions($member);
            }

            $mergeField = new Custom_MailChimp_RBO_MergeField();

            if ($mergeField->install() === true) {
                $this->apply_default_permissions($mergeField);

                $mergeField->set_caption(_M('MailChimp Merge Fields'));
            }

            $this->installAdminDefaults();

            return true;
        } catch (Exception $e) {
            // see if we can rollback
            $this->uninstall();

            return false;
        }
    }

    public function requires($v): array
    {
        return [
            ['name' => 'CRM/Contacts', 'version' => 0],
            ['name' => 'Custom/Api', 'version' => 0]
        ];
    }

    public function uninstall(): bool
    {
        try {
            Base_ThemeCommon::uninstall_default_theme($this->get_type());

            (new Custom_MailChimp_RBO_List())->uninstall();
            (new Custom_MailChimp_RBO_Member())->uninstall();
            (new Custom_MailChimp_RBO_MergeField())->uninstall();

            // Delete variables
            Variable::delete('mailchimp_def_from_email', false);
            Variable::delete('mailchimp_def_from_name', false);
            Variable::delete('mailchimp_def_subject', false);
            Variable::delete('mailchimp_def_language', false);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function version(): array
    {
        return ['0.1'];
    }

    private function apply_default_permissions(RBO_Recordset $recordset): void
    {
        $recordset->add_access('view', 'ACCESS:employee', ['!permission' => 2]);
        $recordset->add_access('add', 'ACCESS:manager');
        $recordset->add_access('edit', 'ACCESS:manager');
        $recordset->add_access('delete', 'ACCESS:manager');
    }

    private function installAdminDefaults(): void
    {
        $user = CRM_ContactsCommon::get_my_record();
        $company = CRM_ContactsCommon::get_main_company() ??
            \sprintf('%s %s', $user['first_name'] ?? '', $user['last_name'] ?? '');

        // Default list
        Variable::set('mailchimp_def_from_email', $user['email'] ?? '');
        Variable::set('mailchimp_def_from_name', $company);
        Variable::set('mailchimp_def_subject', 'Default Subject');
        Variable::set('mailchimp_def_language', 'en');

        // Default contact merge fields.
        $defaultMergeFields = [
            // defau;t in mailchimp
            'email' => ['text', 'EMAIL'],
            'first_name' => ['text', 'FNAME'],
            'last_name' => ['text', 'LNAME'],
            'work_phone' => ['phone', 'PHONE'],
            'address_1' => ['address', 'ADDRESS'],

            'company_name' => ['text', 'COMPANY'],
            'title' => ['text', 'TITLE'],
            'mobile_phone' => ['phone', 'MOBILEPHON'],
            'web_address' => ['url', 'WEBADDRESS'],
            'address_2' => ['address', 'ADDRESS2'],
            'city' => ['address', 'CITY'],
            'country' => ['address', 'COUNTRY'],
            'zone' => ['address', 'ZONE'],
            'postal_code' => ['address', 'POSTALCODE'],
            'home_phone' => ['phone', 'HOMEPHONE'],
            'home_address_1' => ['address', 'HOMEADDR1'],
            'home_address_2' => ['address', 'HOMEADDR2'],
            'home_city' => ['address', 'HOMECITY'],
            'home_country' => ['address', 'HOMECOUNTR'],
            'home_zone' => ['address', 'HOMEZONE'],
            'home_postal_code' => ['address', 'HOMEPOSTAL'],
            'birth_date' => ['birthday', 'BIRTHDATE']
        ];

        $default = 1;
        foreach ($defaultMergeFields as $contact_field => [$type, $tag]) {
            (new Custom_MailChimp_RBO_MergeField())->new_record(\compact('contact_field', 'type', 'tag', 'default'));
        }
    }

}
