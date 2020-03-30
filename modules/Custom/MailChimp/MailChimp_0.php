<?php
declare(strict_types=1);

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 */
class Custom_MailChimp extends Module
{
    use Custom_MailChimp_Traits_Admin;

    public function body(): void
    {
        if (Custom_MailChimp_Bridge_List::getApiEndpoint() === null) {
            echo '<h2>MailChimp not yet configured.</h2><b><a '
                . $this->create_callback_href([$this, 'admin']) . '>Click here to configure.</a></b>';

            return;
        }

        /* @var Utils_RecordBrowser $listRb */
        $listRb = $this->init_module(
            Utils_RecordBrowser::module_name(),
            (new Custom_MailChimp_RBO_List())->table_name()
        );

        $this->display_module($listRb);
    }

    public function contact_addon($contactRecord, $rb_obj): void
    {
        /* @var Utils_RecordBrowser $memberRb */
        $memberRb = (new Custom_MailChimp_RBO_Member())->create_rb_module($this);

        $memberRb->disable_search();
        $memberRb->disable_export();
        $memberRb->disable_fav();
        $memberRb->disable_filters();
        $memberRb->disable_pagination();
        $memberRb->disable_quickjump();
        $memberRb->disable_pdf();
        $memberRb->disable_search();
        $memberRb->disable_watchdog();
        $memberRb->disable_actions();
        $memberRb->set_button('');

        $memberRb->set_defaults(['list' => $contactRecord['id']]);

        $crits = ['contact' => $contactRecord['id']];
        $cols = ['list', 'status'];
        $order = ['merge_id' => 'DESC'];

        $this->display_module($memberRb, [$crits, $cols, $order], 'show_data');
    }

    public function merge_field_addon($record, $rb_obj): void
    {
        /* @var Utils_RecordBrowser $mergeRb */
        $mergeRb = (new Custom_MailChimp_RBO_MergeField())->create_rb_module($this);

        $mergeRb->disable_search();
        $mergeRb->disable_export();
        $mergeRb->disable_fav();
        $mergeRb->disable_filters();
        $mergeRb->disable_pagination();
        $mergeRb->disable_quickjump();
        $mergeRb->disable_pdf();
        $mergeRb->disable_search();
        $mergeRb->disable_watchdog();

        $mergeRb->set_defaults(['list' => $record['id']]);

        $crits = ['list' => $record['id']];
        $cols = ['merge_id', 'contact_field', 'tag'];
        $order = ['merge_id' => 'DESC'];

        $this->display_module($mergeRb, [$crits, $cols, $order], 'show_data');
    }

}
