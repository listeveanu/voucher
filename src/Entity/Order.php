<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $purchased_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $voucher_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchasedDate(): ?DateTimeInterface
    {
        return $this->purchased_date;
    }

    public function setPurchasedDate(DateTimeInterface $purchased_date): self
    {
        $this->purchased_date = $purchased_date;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVoucherId(): ?int
    {
        return $this->voucher_id;
    }

    public function setVoucherId(?int $voucher_id): self
    {
        $this->voucher_id = $voucher_id;

        return $this;
    }
}
