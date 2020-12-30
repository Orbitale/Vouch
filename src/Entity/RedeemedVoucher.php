<?php

declare(strict_types=1);

/*
 * This file is part of the Orbitale Voucher package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Vouch\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Voucher\Repository\RedeemedVoucherRepository")
 * @ORM\Table(name="used_vouchers")
 */
class RedeemedVoucher
{
    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="redeemed_at", type="date_immutable")
     */
    protected $redeemedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Voucher
     *
     * @ORM\ManyToOne(targetEntity="Voucher\Entity\Voucher")
     * @ORM\JoinColumn(name="voucher_id", nullable=false)
     */
    private $voucher;

    /**
     * @var UserInterface
     *
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

    private function __construct()
    {
    }

    public static function create(Voucher $voucher, UserInterface $user): self
    {
        $object = new self();

        $object->voucher = $voucher;
        $object->user = $user;
        $object->redeemedAt = new \DateTimeImmutable();

        return $object;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getRedeemedAt(): \DateTimeInterface
    {
        return $this->redeemedAt;
    }
}
