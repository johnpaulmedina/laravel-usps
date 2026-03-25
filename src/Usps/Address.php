<?php

/**
 * USPS Address data object.
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Address
{
    protected array $addressInfo = [];

    public function setAddress(?string $value): self
    {
        return $this->setField('Address2', $value !== null ? trim($value) : $value);
    }

    public function setApt(?string $value): self
    {
        return $this->setField('Address1', $value !== null ? trim($value) : $value);
    }

    public function setCity(?string $value): self
    {
        return $this->setField('City', $value !== null ? trim($value) : $value);
    }

    public function setState(?string $value): self
    {
        return $this->setField('State', States::toAbbreviation($value));
    }

    public function setZip4(?string $value): self
    {
        if ($value !== null && $value !== '') {
            $value = preg_replace('/\D/', '', $value);
        }
        return $this->setField('Zip4', $value);
    }

    public function setZip5(?string $value): self
    {
        if ($value !== null && $value !== '') {
            // Handle ZIP+4 format like '20500-0005' or '205000005'
            $stripped = preg_replace('/\D/', '', $value);
            if (strlen($stripped) === 9) {
                $this->setField('Zip5', substr($stripped, 0, 5));
                // Only auto-set Zip4 if not already set
                if (empty($this->addressInfo['Zip4'])) {
                    $this->setField('Zip4', substr($stripped, 5, 4));
                }
                return $this;
            }
            $value = $stripped;
        }
        return $this->setField('Zip5', $value);
    }

    public function setFirmName(?string $value): self
    {
        return $this->setField('FirmName', $value !== null ? trim($value) : $value);
    }

    public function setField(string $key, ?string $value): self
    {
        $this->addressInfo[ucwords($key)] = $value;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->addressInfo['Address2'] ?? null;
    }

    public function getApt(): ?string
    {
        return $this->addressInfo['Address1'] ?? null;
    }

    public function getCity(): ?string
    {
        return $this->addressInfo['City'] ?? null;
    }

    public function getState(): ?string
    {
        return $this->addressInfo['State'] ?? null;
    }

    public function getZip5(): ?string
    {
        return $this->addressInfo['Zip5'] ?? null;
    }

    public function getZip4(): ?string
    {
        return $this->addressInfo['Zip4'] ?? null;
    }

    public function getFirmName(): ?string
    {
        return $this->addressInfo['FirmName'] ?? null;
    }

    public function getAddressInfo(): array
    {
        return $this->addressInfo;
    }
}
