<?php

namespace App\Entity;

use App\Repository\VoucherRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoucherRepository::class)
 */
class Voucher
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="smallint")
     */
    private $discount_amount;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $expires_at;

    /**
     * @ORM\Column(type="smallint")
     */
    private $used;

    public function __construct(
        $code,
        $name,
        $description,
        $discount_amount,
        $expires_at,
        $used
    )
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->discount_amount = $discount_amount;
        $this->expires_at = $expires_at;
        $this->used = $used;

        $this->validate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discount_amount;
    }

    public function setDiscountAmount(int $discount_amount): self
    {
        $this->discount_amount = $discount_amount;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expires_at;
    }

    public function setExpiresAt(DateTimeImmutable $expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    public function getUsed(): ?int
    {
        return $this->used;
    }

    public function setUsed(int $used): self
    {
        $this->used = $used;

        return $this;
    }

    private function validate() {
        if (
            empty($this->code)
            || empty($this->name)
            || empty($this->discount_amount)
            || empty($this->expires_at)
        ) {
            throw new \InvalidArgumentException();
        }
    }
}
