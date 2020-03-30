<?php
declare(strict_types=1);

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 *
 * @property \Module $parent
 */
trait Custom_MailChimp_Traits_Admin
{
    public function admin()
    {
        if ($this->is_back()) {
            if ($this->parent->get_type() === 'Base_Admin') {
                $this->parent->reset();
            } else {
                location([]);
            }

            return;
        }

        $this->configureBackButton();

        /* @var Utils_TabbedBrowser $tabs */
        $tabs = $this->init_module(Utils_TabbedBrowser::module_name(), 'mailchimp_admin');
        $tabs->set_tab('API Config', [$this, 'adminTabApiConfig']);
        $tabs->set_tab('List Defaults', [$this, 'adminTabListDefaults']);
        $tabs->set_tab('Default Merge Fields', [$this, 'adminTabMergeTags']);

        echo '<div style="width:75%;text-align:left;">';
        $this->display_module($tabs);
        echo '</div>';

        return true;
    }

    public function adminTabApiConfig()
    {
        $defaults = [];

        /* @var Libs_QuickForm $form */
        $form = $this->init_module(Libs_QuickForm::module_name());
        $form->addElement('text', Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME, __('MailChimp API Key'),
            ['class' => 'api_config']);
        $form->addElement('text', 'api_key_msg', __('MailChimp Root URL'));
        $form->addElement('textarea', 'webhook_url', __('MailChimp WebHook Url'));

        if ($form->validate()) {
            $apiKey = $form->exportValue(Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME);
            Variable::set(Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME, Custom_ApiCommon::encrypt($apiKey));
            Base_StatusBarCommon::message('Settings saved');
        } else {
            $encryptedKey = Variable::get(Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME, false);
            $apiKey = '';
            if ($encryptedKey !== '') {
                $apiKey = Custom_ApiCommon::decrypt(Variable::get(
                    Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME,
                    false
                ));
            }
        }

        $rootUrl = Custom_MailChimp_Bridge_List::getApiEndpoint();

        if ($rootUrl !== null) {
            $apiKeyMsg = $rootUrl;
        } else {
            $apiKeyMsg = 'Your MailChimp Root URL can\'t be determined. Please verify API Key is correct.';
        }

        $defaults['api_key_msg'] = $apiKeyMsg;
        $defaults[Custom_MailChimp_Bridge_List::API_KEY_VARIABLE_NAME] = $apiKey;
        $defaults['webhook_url'] = Custom_MailChimp_Bridge_List::getWebhookUrl();
        $form->setDefaults($defaults);
        $form->freeze(['api_key_msg','webhook_url']);

        $form->display_as_column();

        echo 'Click here for more info: <a target="_blank" href="http://kb.mailchimp.com/accounts/management/about-api-keys">About API Keys</a>';

        Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
    }

    public function adminTabListDefaults()
    {
        $defaults = [];

        /* @var Libs_QuickForm $form */
        $form = $this->init_module(Libs_QuickForm::module_name());
        $form->addElement('text', 'mailchimp_def_from_email', __('List Default From Email'));
        $form->addElement('text', 'mailchimp_def_from_name', __('List Default From Name'));
        $form->addElement('text', 'mailchimp_def_subject', __('List Default Campaign Subject'));
        $languages = ['en' => __('English')];
        $form->addElement('select', 'mailchimp_def_language', __('List Default Language'),
            $languages);
        $form->addElement('textarea', 'mailchimp_permission_reminder',
            __('List Permission Reminder'));

        $variables = [
            'mailchimp_def_from_email',
            'mailchimp_def_from_name',
            'mailchimp_def_subject',
            'mailchimp_def_language',
            'mailchimp_permission_reminder'
        ];
        foreach ($variables as $name) {
            $defaults[$name] = Variable::get($name, false);
        }

        if ($form->validate()) {
            foreach ($variables as $name) {
                $defaults[$name] = $form->exportValue($name);
                Variable::set($name, $defaults[$name]);
            }
            Base_StatusBarCommon::message('Settings saved');
        }

        $form->setDefaults($defaults);
        $form->display_as_column();

        echo 'For more info click: <a target="_blank" href="http://kb.mailchimp.com/campaigns/design/set-up-or-change-email-subject-from-name-and-from-email-address-on-a-campaign#Set-Information-in-List-Name-and-Defaults">List Defaults</a>';
        echo ' and <a target="_blank" href="http://kb.mailchimp.com/accounts/compliance-tips/edit-the-permission-reminder">Permission Reminder</a>';
        Base_ActionBarCommon::add('save', __('Save'), $form->get_submit_form_href());
    }

    public function adminTabMergeTags()
    {
        /* @var Utils_RecordBrowser $mergeRb */
        $mergeRb = $this->init_module(
            Utils_RecordBrowser::module_name(),
            (new Custom_MailChimp_RBO_MergeField())->table_name()
        );

        $mergeRb->disable_search();
        $mergeRb->disable_export();
        $mergeRb->disable_fav();
        $mergeRb->disable_filters();
        $mergeRb->disable_pagination();
        $mergeRb->disable_quickjump();
        $mergeRb->disable_pdf();
        $mergeRb->disable_search();
        $mergeRb->disable_watchdog();
        $mergeRb->set_defaults(['default' => 1]);

        $crits = ['default' => 1];
        $cols = ['contact_field', 'tag', 'type'];
        $order = [];

        $this->display_module($mergeRb, [$crits, $cols, $order], 'show_data');
    }

    private function configureBackButton(): void
    {
        if (isset($_REQUEST['back_location']) === false) {
            Base_ActionBarCommon::add('back', __('Back'), $this->create_back_href());

            return;
        }

        $backLocation = $_REQUEST['back_location'];
        Base_ActionBarCommon::add('back', __('Back'), Base_BoxCommon::create_href(
            $this, $backLocation['module'], $backLocation['func'],
            $backLocation['args'] ?? []
        ));
    }

}
