<?php

declare(strict_types=1);

namespace App\Main\Domain\Entity\Update;

use App\Shared\Domain\Service\UlidService;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'updates_successful_payment')]
class SuccessfulPayment
{
    #[ORM\Id, ORM\Column(type: 'string', length: 26), ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    /**
     * @param string $currency
     * @param int $totalAmount
     * @param array<string, mixed> $invoicePayload
     * @param string|null $shippingOptionId
     * @param OrderInfo|null $orderInfo
     * @param string $telegramPaymentChargeId
     * @param string $providerPaymentChargeId
     */
    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $currency,
        #[ORM\Column(type: 'integer')]
        private readonly int $totalAmount,
        #[ORM\Column(type: 'json')]
        private readonly array $invoicePayload,
        #[ORM\Column(type: 'string', nullable: true)]
        private readonly ?string $shippingOptionId,
        #[ORM\OneToOne(targetEntity: OrderInfo::class)]
        #[ORM\JoinColumn(name: 'order_info_id', referencedColumnName: 'id')]
        private readonly ?OrderInfo $orderInfo,
        #[ORM\Column(type: 'string')]
        private readonly string $telegramPaymentChargeId,
        #[ORM\Column(type: 'string')]
        private readonly string $providerPaymentChargeId,
    ) {
        $this->id = UlidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    /** @return array<string, mixed> */
    public function getInvoicePayload(): array
    {
        return $this->invoicePayload;
    }

    public function getShippingOptionId(): ?string
    {
        return $this->shippingOptionId;
    }

    public function getOrderInfo(): ?OrderInfo
    {
        return $this->orderInfo;
    }

    public function getTelegramPaymentChargeId(): string
    {
        return $this->telegramPaymentChargeId;
    }

    public function getProviderPaymentChargeId(): string
    {
        return $this->providerPaymentChargeId;
    }
}
