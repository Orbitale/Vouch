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

namespace Orbitale\Vouch\Handler;

use Orbitale\Vouch\Entity\Voucher;
use Orbitale\Vouch\Exception\ExceededNumberOfUsesForVoucher;
use Orbitale\Vouch\Repository\RedeemedVoucherRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class MaxNumberOfUsesVoucherConsumer implements VoucherConsumerInterface
{
    private RedeemedVoucherRepository $redeemedVoucherRepository;

    public function __construct(RedeemedVoucherRepository $redeemedVoucherRepository)
    {
        $this->redeemedVoucherRepository = $redeemedVoucherRepository;
    }

    public static function getPriority(): int
    {
        return 90;
    }

    public function supports(Voucher $voucher, UserInterface $user): bool
    {
        return true;
    }

    public function handle(Voucher $voucher, UserInterface $user): void
    {
        $maxNumberOfUses = $voucher->getMaxNumberOfUses();

        if ($maxNumberOfUses > 0) {
            $used = $this->redeemedVoucherRepository->getNumberOfVouchersUsedForType($voucher->getType());

            if ($used >= $maxNumberOfUses) {
                throw new ExceededNumberOfUsesForVoucher($voucher, $user);
            }
        }
    }
}
