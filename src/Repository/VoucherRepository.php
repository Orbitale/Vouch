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
use Orbitale\Vouch\Entity\Voucher;

/**
 * @method null|Voucher find($id, $lockMode = null, $lockVersion = null)
 * @method null|Voucher findOneBy(array $criteria, array $orderBy = null)
 * @method Voucher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Voucher[]    findAll()
 */
class VoucherRepository extends EntityRepository
{
    public function findByCode(string $code): ?Voucher
    {
        return $this->createQueryBuilder('voucher')
            ->where('voucher.uniqueCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
