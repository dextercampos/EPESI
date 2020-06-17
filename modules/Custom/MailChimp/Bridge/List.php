<?php
declare(strict_types=1);

use Dex\MailChimp\DTO\Audience;
use Dex\MailChimp\DTO\CampaignDefault;
use Dex\MailChimp\DTO\Contact;
use Dex\MailChimp\DTO\MergeField;
use DrewM\MailChimp\MailChimp;

defined('_VALID_ACCESS') || die('Direct access forbidden');

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 *
 * Bridge library classes for epesi use.
 */
final class Custom_MailChimp_Bridge_List
{
    public const API_KEY_VARIABLE_NAME = 'mailchimp_api_key';

    private static $mailchimp;

    public static function getAdminEndpoint(bool $throwError = false): ?string
    {
        try {
            return self::getMailChimp()->getAdminEndpoint();
        } catch (Exception $exception) {
            if ($throwError === true) {
                throw $exception;
            }
        }

        return null;
    }

    public static function getApiEndpoint(bool $throwError = false): ?string
    {
        try {
            return self::getMailChimp()->getApiEndpoint();
        } catch (Exception $exception) {
            if ($throwError === true) {
                throw $exception;
            }
        }

        return null;
    }

    public static function getMailChimp(): MailChimp
    {
        if (self::$mailchimp === null) {
            $apiKey = Custom_ApiCommon::decrypt(Variable::get(self::API_KEY_VARIABLE_NAME));
            self::$mailchimp = new MailChimp($apiKey);
        }

        return self::$mailchimp;
    }

    public static function getWebhookUrl(): string
    {
        return \sprintf(
            '%s/modules/Custom/MailChimp/webhook.php?token=%s',
            \get_epesi_url(),
            Custom_ApiCommon::getApiKey()
        );
    }

    public static function listCreate(array $listData): bool
    {
        $listId = $listData['id'];

        $audience = self::mapListToAudience($listData);

        /** @var array $response */
        $response = self::getMailChimp()->post('lists', $audience->toArray());

        $externalId = $response['id'] ?? null;

        if ($externalId === null) {
            throw new Exception($response['title'] ?? 'Error occurred', $response['status'] ?? 500);
        }

        (new Custom_MailChimp_RBO_List())->update_record($listId, [
            'mailchimp_id' => $externalId,
            'linked' => 1,
            'web_id' => $response['web_id'] ?? '',
        ]);

        // add webhook
        $webhookAdded = self::newWebhook($externalId);

        // add merge fields
        self::addDefaultMergeFields($externalId, $listId);

        return $webhookAdded;
    }

    public static function listDelete($id)
    {
        /** @var array $response */
        $response = self::getMailChimp()->delete(\sprintf('lists/%s', $id));

        if ($response['type'] !== null) {
            throw new Exception($response['title'] ?? 'Error occurred', $response['status'] ?? 500);
        }

        return true;
    }

    public static function listLinkExisting(array $listData): bool
    {
        $listId = $listData['id'];
        $externalId = $listData['mailchimp_id'] ?? null;
        if ($externalId === null) {
            return false;
        }

        $audience = self::mapListToAudience($listData);

        /** @var array $response */
        $response = self::getMailChimp()->patch(\sprintf('lists/%s', $externalId), $audience->toArray());

        $externalId = $response['id'] ?? null;

        if ($externalId === null) {
            throw new Exception($response['title'] ?? 'Error occurred', $response['status'] ?? 500);
        }

        (new Custom_MailChimp_RBO_List())->update_record($listId, [
            'mailchimp_id' => $externalId,
            'linked' => 1,
            'web_id' => $response['web_id'] ?? '',
        ]);

        // add webhook
        $webhookAdded = self::newWebhook($externalId);

        // add merge fields
        self::addDefaultMergeFields($externalId, $listId);

        return $webhookAdded;
    }

    public static function listUpdate($id, array $listData): bool
    {
        $audience = self::mapListToAudience($listData);

        /** @var array $response */
        $response = self::getMailChimp()->patch(\sprintf('lists/%s', $id), $audience->toArray());

        $externalId = $response['id'] ?? null;

        if ($externalId === null) {
            throw new Exception($response['title'] ?? 'Error occurred', $response['status'] ?? 500);
        }

        return true;
    }

    public static function newWebhook($listId): bool
    {
        $data = [
            'url' => self::getWebhookUrl(),
            'events' => [
                'subscribe' => true,
                'unsubscribe' => true,
            ],
            'sources' => [
                'user' => true,
                'admin' => true,
                'api' => true,
            ],
        ];

        self::getMailChimp()->post(\sprintf('lists/%s/webhooks', $listId), $data);

        return true;
    }

    private static function addDefaultMergeFields($externalListId, $listId): void
    {
        $mergeFieldRbo = new Custom_MailChimp_RBO_MergeField();
        $mergeFields = $mergeFieldRbo->get_records(['list_id' => $listId]);

        foreach ($mergeFields as $mergeFieldData) {
            $mergeField = new MergeField();
            $mergeField
                ->setName($mergeFieldData->get_val('contact_field'))
                ->setTag($mergeFieldData->tag)
                ->setType($mergeFieldData->type)
                ->setListId($externalListId);

            /** @var array $response */
            $response = self::getMailChimp()->post(
                \sprintf('lists/%s/merge-fields', $externalListId),
                $mergeField->toArray()
            );

            $mergeId = $response['merge_id'] ?? null;

            if ($mergeId === null) {
                continue;
            }

            $mergeFieldData->merge_id = $mergeId;
            $mergeFieldData->save();
        }
    }

    private static function mapListToAudience(array $listData): Audience
    {
        $audience = new Audience();
        $contact = new Contact();
        $campaignDefaults = new CampaignDefault();

        $audience->setName($listData['name'])
            ->setPermissionReminder($listData['permission_reminder'])
            ->setEmailTypeOption((bool)$listData['email_type_option'])
            ->setContact($contact)
            ->setCampaignDefaults($campaignDefaults);

        $contact->setCompany($listData['company_name']);
        $contact->setAddress1($listData['address_1']);
        $contact->setAddress2($listData['address_2']);
        $contact->setCity($listData['city']);
        $contact->setState($listData['state']);
        $contact->setZip($listData['postal_code']);
        $contact->setCountry($listData['country']);
        $contact->setPhone($listData['phone']);

        $campaignDefaults->setFromEmail($listData['from_email']);
        $campaignDefaults->setFromName($listData['from_name']);
        $campaignDefaults->setSubject($listData['subject']);

        return $audience;
    }
}
