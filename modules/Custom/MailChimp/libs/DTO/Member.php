<?php
declare(strict_types=1);

namespace Dex\MailChimp\DTO;

use Dex\MailChimp\Interfaces\ArrayableInterface;

final class Member implements ArrayableInterface
{
    /** @var string */
    private $email_address;

    /** @var string */
    private $email_type;

    /** @var string */
    private $status;

    /** @var string */
    private $subscriber_hash;

    /** @var string */
    private $unsubscribe_reason;

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->email_address;
    }

    /**
     * @return string
     */
    public function getEmailType(): string
    {
        return $this->email_type;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSubscriberHash(): string
    {
        return $this->subscriber_hash;
    }

    /**
     * @return string
     */
    public function getUnsubscribeReason(): string
    {
        return $this->unsubscribe_reason;
    }

    /**
     * @param string $email_address
     *
     * @return Member
     */
    public function setEmailAddress(string $email_address): Member
    {
        $this->email_address = $email_address;

        return $this;
    }

    /**
     * @param string $email_type
     *
     * @return Member
     */
    public function setEmailType(string $email_type): Member
    {
        $this->email_type = $email_type;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return Member
     */
    public function setStatus(string $status): Member
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $subscriber_hash
     *
     * @return Member
     */
    public function setSubscriberHash(string $subscriber_hash): Member
    {
        $this->subscriber_hash = $subscriber_hash;

        return $this;
    }

    /**
     * @param string $unsubscribe_reason
     *
     * @return Member
     */
    public function setUnsubscribeReason(string $unsubscribe_reason): Member
    {
        $this->unsubscribe_reason = $unsubscribe_reason;

        return $this;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
