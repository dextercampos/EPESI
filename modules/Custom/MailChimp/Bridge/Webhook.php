<?php
declare(strict_types=1);

use DrewM\MailChimp\Webhook;

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 *
 * Bridge library classes for epesi use.
 */
final class Custom_MailChimp_Bridge_Webhook
{
    public static function applySubscriptions()
    {
        Webhook::subscribe('subscribe', [self::class, 'subscribe']);
        Webhook::subscribe('unsubscribe', [self::class, 'unsubscribe']);
    }

    public static function subscribe($data)
    {
        $mailchimpId = $data['list_id'];

        $lists = (new Custom_MailChimp_RBO_List())->get_records(['mailchimp_id' => $mailchimpId])[0] ?? null;
        $list = reset($lists);

        if ($list === false) {
            return;
        }

        // Check if already in database.
        $members = (new Custom_MailChimp_RBO_Member())->get_records([
                'list' => $list->id,
                'email' => $data['email'],
            ])[0] ?? null;
        $member = reset($members);

        // If member already in database list do nothing.
        if ($member !== false) {
            return;
        }

        $contactId = CRM_ContactsCommon::get_record_by_email($data['email'])[1] ?? null;
        $contactArray = [];

        $mergeFields = (new Custom_MailChimp_RBO_MergeField())->getForList($list->id);
        $mergeData = $data['merges'] ?? [];

        foreach ($mergeFields as $mergeField) {
            if ($contactId !== null) {
                continue;
            }

            $contactArray[$mergeField->contact_field] = $mergeData[$mergeField->tag] ?? null;
        }

        if ($contactId === null) {
            $contactId = Utils_RecordBrowserCommon::new_record('contact', $contactArray);
        }

        $member = (new Custom_MailChimp_RBO_Member())->new_record();
        $member->list = $list->id;
        $member->email = $data['email'] ?? null;
        $member->email_type = $data['email_type'] ?? null;
        $member->status = $data['status'] ?? 'subscribed';
        $member->subscriber_hash = \md5(\strtolower($member->email));
        $member->contact = $contactId;
        $member->save();
    }

    public static function unsubscribe($data)
    {
        $mailchimpId = $data['list_id'];

        $lists = (new Custom_MailChimp_RBO_List())->get_records(['mailchimp_id' => $mailchimpId])[0] ?? null;
        $list = reset($lists);

        if ($list === false) {
            return;
        }

        // Check if already in database.
        $members = (new Custom_MailChimp_RBO_Member())->get_records([
                'list' => $list->id,
                'email' => $data['email']
            ])[0] ?? null;
        $member = reset($members);

        // If member not in database list do nothing.
        if ($member === false) {
            return;
        }

        $member->status = $data['status'] ?? 'unsubscribed';
        $member->unsubscribe_reason = $data['unsubscribe_reason'] ?? '';
        $member->save();
    }
}
