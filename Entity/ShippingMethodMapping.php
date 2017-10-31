<?php

namespace Loevgaard\PakkelabelsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * This entity maps any (shipping method) string to a product code and optional service codes.
 *
 * @ORM\Entity
 * @ORM\Table(name="pakkelabels_shipping_method_mapping")
 * @UniqueEntity("source")
 */
class ShippingMethodMapping
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
     *
     * @ORM\Column(type="string")
     */
    protected $productCode;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $serviceCodes;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return ShippingMethodMapping
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return ShippingMethodMapping
     */
    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     *
     * @return ShippingMethodMapping
     */
    public function setProductCode(string $productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @return array
     */
    public function getServiceCodes(): ?array
    {
        return $this->serviceCodes;
    }

    /**
     * @param array $serviceCodes
     *
     * @return ShippingMethodMapping
     */
    public function setServiceCodes(array $serviceCodes)
    {
        $this->serviceCodes = $serviceCodes;

        return $this;
    }
}
