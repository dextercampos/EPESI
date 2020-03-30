<?php
declare(strict_types=1);

namespace Dex\MailChimp\DTO;

use Dex\MailChimp\Interfaces\ArrayableInterface;

final class Audience implements ArrayableInterface
{
    /** @var \Dex\MailChimp\DTO\CampaignDefault */
    private $campaign_defaults;

    /** @var \Dex\MailChimp\DTO\Contact */
    private $contact;

    /** @var bool */
    private $email_type_option = false;

    /** @var string */
    private $list_id;

    /** @var string */
    private $name;

    /** @var string */
    private $permission_reminder;

    /**
     * @return \Dex\MailChimp\DTO\CampaignDefault
     */
    public function getCampaignDefaults(): \Dex\MailChimp\DTO\CampaignDefault
    {
        return $this->campaign_defaults;
    }

    /**
     * @return \Dex\MailChimp\DTO\Contact
     */
    public function getContact(): \Dex\MailChimp\DTO\Contact
    {
        return $this->contact;
    }

    /**
     * @return bool
     */
    public function getEmailTypeOption(): bool
    {
        return $this->email_type_option;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPermissionReminder(): string
    {
        return $this->permission_reminder;
    }

    /**
     * @param \Dex\MailChimp\DTO\CampaignDefault $campaign_defaults
     *
     * @return Audience
     */
    public function setCampaignDefaults(\Dex\MailChimp\DTO\CampaignDefault $campaign_defaults): Audience
    {
        $this->campaign_defaults = $campaign_defaults;

        return $this;
    }

    /**
     * @param \Dex\MailChimp\DTO\Contact $contact
     *
     * @return Audience
     */
    public function setContact(\Dex\MailChimp\DTO\Contact $contact): Audience
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @param bool $email_type_option
     *
     * @return Audience
     */
    public function setEmailTypeOption(bool $email_type_option): Audience
    {
        $this->email_type_option = $email_type_option;

        return $this;
    }

    /**
     * @param string $list_id
     *
     * @return Audience
     */
    public function setListId(string $list_id): Audience
    {
        $this->list_id = $list_id;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Audience
     */
    public function setName(string $name): Audience
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $permission_reminder
     *
     * @return Audience
     */
    public function setPermissionReminder(string $permission_reminder): Audience
    {
        $this->permission_reminder = $permission_reminder;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'campaign_defaults' => $this->getCampaignDefaults()->toArray(),
            'contact' => $this->getContact()->toArray(),
            'email_type_option' => $this->getEmailTypeOption(),
            'name' => $this->getName(),
            'permission_reminder' => $this->getPermissionReminder()
        ];
    }
}
