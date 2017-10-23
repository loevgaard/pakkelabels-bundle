<?php

namespace Loevgaard\PakkelabelsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This entity maps any (country) string to an ISO 3166 Alpha 2 country code.
 *
 * @ORM\Entity
 * @ORM\Table(name="pakkelabels_country_mapping")
 */
class CountryMapping
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $source;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="2")
     *
     * @ORM\Column(type="string", length=2)
     */
    protected $countryCode;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return CountryMapping
     */
    public function setId(int $id): CountryMapping
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return CountryMapping
     */
    public function setSource(string $source): CountryMapping
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return CountryMapping
     */
    public function setCountryCode(string $countryCode): CountryMapping
    {
        $this->countryCode = $countryCode;

        return $this;
    }
}
