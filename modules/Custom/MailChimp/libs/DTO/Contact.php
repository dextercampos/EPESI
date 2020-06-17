<?php
declare(strict_types=1);

namespace Dex\MailChimp\DTO;

use Dex\MailChimp\Interfaces\ArrayableInterface;

final class Contact implements ArrayableInterface
{
    /** @var string */
    private $address1;

    /** @var string */
    private $address2;

    /** @var string */
    private $city;

    /** @var string */
    private $company;

    /** @var string */
    private $country;

    /** @var string */
    private $phone;

    /** @var string */
    private $state;

    /** @var string */
    private $zip;

    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $address1
     *
     * @return Contact
     */
    public function setAddress1(string $address1): Contact
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * @param string $address2
     *
     * @return Contact
     */
    public function setAddress2(string $address2): Contact
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @param string $city
     *
     * @return Contact
     */
    public function setCity(string $city): Contact
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @param string $company
     *
     * @return Contact
     */
    public function setCompany(string $company): Contact
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @param string $country
     *
     * @return Contact
     */
    public function setCountry(string $country): Contact
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @param string $phone
     *
     * @return Contact
     */
    public function setPhone(string $phone): Contact
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @param string $state
     *
     * @return Contact
     */
    public function setState(string $state): Contact
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @param string $zip
     *
     * @return Contact
     */
    public function setZip(string $zip): Contact
    {
        $this->zip = $zip;

        return $this;
    }

    public function toArray(): array
    {
        // TODO; don't be lazy. LOL!
        return \get_object_vars($this);
    }
}
