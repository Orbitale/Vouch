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

namespace Orbitale\Vouch\Repository;

use Doctrine\ORM\EntityRepository;
use Orbitale\Vouch\Entity\RedeemedVoucher;
use Orbitale\Vouch\Entity\Voucher;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method null|RedeemedVoucher find($id, $lockMode = null, $lockVersion = null)
 * @method null|RedeemedVoucher findOneBy(array $criteria, array $orderBy = null)
 * @method RedeemedVoucher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method RedeemedVoucher[]    findAll()
 */
class RedeemedVoucherRepository extends EntityRepository
{
    public function getNumberOfVouchersUsedForType(string $type): int
    {
        return (int) $this->createQueryBuilder('redeemed_voucher')
            ->select('COUNT(redeemed_voucher.id) as number_of_vouchers')
            ->leftJoin('redeemed_voucher.voucher', 'voucher')
            ->where('voucher.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return array|RedeemedVoucher[]
     */
    public function findByVoucherAndUser(Voucher $voucher, UserInterface $user)
    {
        return $this->createQueryBuilder('redeemed_voucher')
            ->where('redeemed_voucher.voucher = :voucher')
            ->andWhere('redeemed_voucher.user = :user')
            ->setParameters([
                'voucher' => $voucher,
                'user' => $user,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
