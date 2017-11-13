<?php

namespace Loevgaard\PakkelabelsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="pakkelabels_labels")
 */
class Label
{
    const STATUS_PENDING_NORMALIZATION = 'pending_normalization';
    const STATUS_PENDING_CREATION = 'pending_creation';
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    const LABEL_FORMAT_A4_PDF = 'a4_pdf';
    const LABEL_FORMAT_10_X_19_PDF = '10x19_pdf';
    const LABEL_FORMAT_PNG = 'png';
    const LABEL_FORMAT_ZPL = 'zpl';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * This is the shipment id from Pakkelabels.
     *
     * @var int
     *
     * @ORM\Column(type="integer", unique=true, nullable=true)
     */
    protected $externalId;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $error;

    /**
     * The shipping method property is used in normalization. We check the mappings table to see
     * if there is a shipping method matching this. If there is, we will populate the product code
     * and services codes properties.
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $shippingMethod;

    /**
     * This property indiciates whether this label is a return label or not.
     *
     * @var bool
     *
     * @Assert\NotNull()
     *
     * @ORM\Column(type="boolean")
     */
    protected $returnLabel;

    /**************************
     * Pakkelabels properties *
     *************************/
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $orderId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $reference;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $source;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     */
    protected $ownAgreement;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $labelFormat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $productCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $serviceCodes;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     */
    protected $automaticSelectServicePoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $servicePointId;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     */
    protected $smsNotification;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     */
    protected $emailNotification;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderAddress1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $senderAddress2;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderCountryCode;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderZipCode;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $senderAttention;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $senderEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $senderTelephone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $senderMobile;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverAddress1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $receiverAddress2;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverCountryCode;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverZipCode;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $receiverAttention;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string")
     */
    protected $receiverEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $receiverTelephone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $receiverMobile;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $receiverInstruction;

    public function __construct()
    {
        $this->status = static::STATUS_PENDING_NORMALIZATION;
        $this->smsNotification = false;
        $this->emailNotification = false;
        $this->automaticSelectServicePoint = true;
        $this->returnLabel = false;
    }

    public function arrayForApi(): array
    {
        $data = [
            'order_id' => $this->orderId,
            'reference' => $this->reference,
            'source' => $this->source,
            'own_agreement' => $this->ownAgreement,
            'label_format' => $this->labelFormat,
            'product_code' => $this->productCode,
            'service_codes' => $this->serviceCodes,
            'automatic_select_service_point' => $this->automaticSelectServicePoint,
            'sms_notification' => $this->smsNotification,
            'email_notification' => $this->emailNotification,
            'service_point' => [
                'id' => $this->servicePointId,
            ],
            'sender' => [
                'name' => $this->senderName,
                'address1' => $this->senderAddress1,
                'address2' => $this->senderAddress2,
                'country_code' => $this->senderCountryCode,
                'zipcode' => $this->senderZipCode,
                'city' => $this->senderCity,
                'attention' => $this->senderAttention,
                'email' => $this->senderEmail,
                'telephone' => $this->senderTelephone,
                'mobile' => $this->senderMobile,
            ],
            'receiver' => [
                'name' => $this->receiverName,
                'address1' => $this->receiverAddress1,
                'address2' => $this->receiverAddress2,
                'country_code' => $this->receiverCountryCode,
                'zipcode' => $this->receiverZipCode,
                'city' => $this->receiverCity,
                'attention' => $this->receiverAttention,
                'email' => $this->receiverEmail,
                'telephone' => $this->receiverTelephone,
                'mobile' => $this->receiverMobile,
                'instruction' => $this->receiverInstruction,
            ],
            'parcels' => [
                [
                    'weight' => 1000,
                ],
            ],
        ];

        return $data;
    }

    public function markAsError(string $error)
    {
        $this->error = $error;
        $this->status = static::STATUS_ERROR;
    }

    public function markAsSuccess()
    {
        $this->error = null;
        $this->status = static::STATUS_SUCCESS;
    }

    public function resetStatus()
    {
        $this->error = null;
        $this->status = static::STATUS_PENDING_NORMALIZATION;
    }

    public function isStatus(string $status): bool
    {
        return $this->status === $status;
    }

    public function isSuccess(): bool
    {
        return $this->isStatus(static::STATUS_SUCCESS);
    }

    public function isError(): bool
    {
        return $this->isStatus(static::STATUS_ERROR);
    }

    public function getStatusTranslationKey(): string
    {
        return 'label.status.'.$this->status;
    }

    /**
     * Returns the available label formats.
     *
     * @return array
     */
    public static function getLabelFormats(): array
    {
        return [
            self::LABEL_FORMAT_10_X_19_PDF => self::LABEL_FORMAT_10_X_19_PDF,
            self::LABEL_FORMAT_A4_PDF => self::LABEL_FORMAT_A4_PDF,
            self::LABEL_FORMAT_PNG => self::LABEL_FORMAT_PNG,
            self::LABEL_FORMAT_ZPL => self::LABEL_FORMAT_ZPL,
        ];
    }

    /**
     * Returns the available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING_NORMALIZATION => self::STATUS_PENDING_NORMALIZATION,
            self::STATUS_PENDING_CREATION => self::STATUS_PENDING_CREATION,
            self::STATUS_SUCCESS => self::STATUS_SUCCESS,
            self::STATUS_ERROR => self::STATUS_ERROR,
        ];
    }

    /*********************
     * Getters / Setters *
     ********************/

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
     * @return Label
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     *
     * @return Label
     */
    public function setExternalId(int $externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Label
     */
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string $error
     *
     * @return Label
     */
    public function setError(string $error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     *
     * @return Label
     */
    public function setShippingMethod(string $shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReturnLabel(): bool
    {
        return $this->returnLabel;
    }

    /**
     * @param bool $returnLabel
     *
     * @return Label
     */
    public function setReturnLabel(bool $returnLabel)
    {
        $this->returnLabel = $returnLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     *
     * @return Label
     */
    public function setOrderId(string $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return Label
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;

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
     * @return Label
     */
    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOwnAgreement(): bool
    {
        return $this->ownAgreement;
    }

    /**
     * @param bool $ownAgreement
     *
     * @return Label
     */
    public function setOwnAgreement(bool $ownAgreement)
    {
        $this->ownAgreement = $ownAgreement;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelFormat(): ?string
    {
        return $this->labelFormat;
    }

    /**
     * @param string $labelFormat
     *
     * @return Label
     */
    public function setLabelFormat(string $labelFormat)
    {
        $this->labelFormat = $labelFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     *
     * @return Label
     */
    public function setProductCode(string $productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceCodes(): string
    {
        return $this->serviceCodes;
    }

    /**
     * @param string $serviceCodes
     *
     * @return Label
     */
    public function setServiceCodes(string $serviceCodes)
    {
        $this->serviceCodes = $serviceCodes;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutomaticSelectServicePoint(): bool
    {
        return $this->automaticSelectServicePoint;
    }

    /**
     * @param bool $automaticSelectServicePoint
     *
     * @return Label
     */
    public function setAutomaticSelectServicePoint(bool $automaticSelectServicePoint)
    {
        $this->automaticSelectServicePoint = $automaticSelectServicePoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getServicePointId(): string
    {
        return $this->servicePointId;
    }

    /**
     * @param string $servicePointId
     *
     * @return Label
     */
    public function setServicePointId(string $servicePointId)
    {
        $this->servicePointId = $servicePointId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSmsNotification(): bool
    {
        return $this->smsNotification;
    }

    /**
     * @param bool $smsNotification
     *
     * @return Label
     */
    public function setSmsNotification(bool $smsNotification)
    {
        $this->smsNotification = $smsNotification;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailNotification(): bool
    {
        return $this->emailNotification;
    }

    /**
     * @param bool $emailNotification
     *
     * @return Label
     */
    public function setEmailNotification(bool $emailNotification)
    {
        $this->emailNotification = $emailNotification;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     *
     * @return Label
     */
    public function setSenderName(string $senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderAddress1(): string
    {
        return $this->senderAddress1;
    }

    /**
     * @param string $senderAddress1
     *
     * @return Label
     */
    public function setSenderAddress1(string $senderAddress1)
    {
        $this->senderAddress1 = $senderAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderAddress2(): ?string
    {
        return $this->senderAddress2;
    }

    /**
     * @param string $senderAddress2
     *
     * @return Label
     */
    public function setSenderAddress2(string $senderAddress2)
    {
        $this->senderAddress2 = $senderAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderCountryCode(): string
    {
        return $this->senderCountryCode;
    }

    /**
     * @param string $senderCountryCode
     *
     * @return Label
     */
    public function setSenderCountryCode(string $senderCountryCode)
    {
        $this->senderCountryCode = $senderCountryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderZipCode(): string
    {
        return $this->senderZipCode;
    }

    /**
     * @param string $senderZipCode
     *
     * @return Label
     */
    public function setSenderZipCode(string $senderZipCode)
    {
        $this->senderZipCode = $senderZipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderCity(): string
    {
        return $this->senderCity;
    }

    /**
     * @param string $senderCity
     *
     * @return Label
     */
    public function setSenderCity(string $senderCity)
    {
        $this->senderCity = $senderCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderAttention(): ?string
    {
        return $this->senderAttention;
    }

    /**
     * @param string $senderAttention
     *
     * @return Label
     */
    public function setSenderAttention(string $senderAttention)
    {
        $this->senderAttention = $senderAttention;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     *
     * @return Label
     */
    public function setSenderEmail(string $senderEmail)
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderTelephone(): ?string
    {
        return $this->senderTelephone;
    }

    /**
     * @param string $senderTelephone
     *
     * @return Label
     */
    public function setSenderTelephone(string $senderTelephone)
    {
        $this->senderTelephone = $senderTelephone;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderMobile(): ?string
    {
        return $this->senderMobile;
    }

    /**
     * @param string $senderMobile
     *
     * @return Label
     */
    public function setSenderMobile(string $senderMobile)
    {
        $this->senderMobile = $senderMobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    /**
     * @param string $receiverName
     *
     * @return Label
     */
    public function setReceiverName(string $receiverName)
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverAddress1(): string
    {
        return $this->receiverAddress1;
    }

    /**
     * @param string $receiverAddress1
     *
     * @return Label
     */
    public function setReceiverAddress1(string $receiverAddress1)
    {
        $this->receiverAddress1 = $receiverAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverAddress2(): ?string
    {
        return $this->receiverAddress2;
    }

    /**
     * @param string $receiverAddress2
     *
     * @return Label
     */
    public function setReceiverAddress2(string $receiverAddress2)
    {
        $this->receiverAddress2 = $receiverAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverCountryCode(): string
    {
        return $this->receiverCountryCode;
    }

    /**
     * @param string $receiverCountryCode
     *
     * @return Label
     */
    public function setReceiverCountryCode(string $receiverCountryCode)
    {
        $this->receiverCountryCode = $receiverCountryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverZipCode(): string
    {
        return $this->receiverZipCode;
    }

    /**
     * @param string $receiverZipCode
     *
     * @return Label
     */
    public function setReceiverZipCode(string $receiverZipCode)
    {
        $this->receiverZipCode = $receiverZipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverCity(): string
    {
        return $this->receiverCity;
    }

    /**
     * @param string $receiverCity
     *
     * @return Label
     */
    public function setReceiverCity(string $receiverCity)
    {
        $this->receiverCity = $receiverCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverAttention(): ?string
    {
        return $this->receiverAttention;
    }

    /**
     * @param string $receiverAttention
     *
     * @return Label
     */
    public function setReceiverAttention(string $receiverAttention)
    {
        $this->receiverAttention = $receiverAttention;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverEmail(): string
    {
        return $this->receiverEmail;
    }

    /**
     * @param string $receiverEmail
     *
     * @return Label
     */
    public function setReceiverEmail(string $receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverTelephone(): ?string
    {
        return $this->receiverTelephone;
    }

    /**
     * @param string $receiverTelephone
     *
     * @return Label
     */
    public function setReceiverTelephone(string $receiverTelephone)
    {
        $this->receiverTelephone = $receiverTelephone;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverMobile(): ?string
    {
        return $this->receiverMobile;
    }

    /**
     * @param string $receiverMobile
     *
     * @return Label
     */
    public function setReceiverMobile(string $receiverMobile)
    {
        $this->receiverMobile = $receiverMobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverInstruction(): ?string
    {
        return $this->receiverInstruction;
    }

    /**
     * @param string $receiverInstruction
     *
     * @return Label
     */
    public function setReceiverInstruction(string $receiverInstruction)
    {
        $this->receiverInstruction = $receiverInstruction;

        return $this;
    }
}
