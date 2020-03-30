<?php
declare(strict_types=1);

namespace Dex\MailChimp\DTO;

use Dex\MailChimp\Interfaces\ArrayableInterface;

final class CampaignDefault implements ArrayableInterface
{
    /** @var string */
    private $from_email;

    /** @var string */
    private $from_name;

    /**
     * English only for now.
     *
     * @var string
     */
    private $language = 'en';

    /** @var string */
    private $subject;

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->from_email;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->from_name;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $from_email
     *
     * @return CampaignDefault
     */
    public function setFromEmail(string $from_email): CampaignDefault
    {
        $this->from_email = $from_email;

        return $this;
    }

    /**
     * @param string $from_name
     *
     * @return CampaignDefault
     */
    public function setFromName(string $from_name): CampaignDefault
    {
        $this->from_name = $from_name;

        return $this;
    }

    /**
     * @param string $subject
     *
     * @return CampaignDefault
     */
    public function setSubject(string $subject): CampaignDefault
    {
        $this->subject = $subject;

        return $this;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
