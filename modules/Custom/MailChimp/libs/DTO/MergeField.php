<?php
declare(strict_types=1);

namespace Dex\MailChimp\DTO;

use Dex\MailChimp\Interfaces\ArrayableInterface;

final class MergeField implements ArrayableInterface
{
    /** @var string */
    private $list_id;

    /** @var string */
    private $name;

    /** @var string */
    private $tag;

    /** @var string */
    private $type;

    /**
     * @return string
     */
    public function getListId(): string
    {
        return $this->list_id;
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
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $list_id
     *
     * @return MergeField
     */
    public function setListId(string $list_id): MergeField
    {
        $this->list_id = $list_id;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return MergeField
     */
    public function setName(string $name): MergeField
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $tag
     *
     * @return MergeField
     */
    public function setTag(string $tag): MergeField
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return MergeField
     */
    public function setType(string $type): MergeField
    {
        $this->type = $type;

        return $this;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
