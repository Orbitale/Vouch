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
use Orbitale\Vouch\Exception\UserHasAlreadyRedeemedThisVoucher;
use Orbitale\Vouch\Repository\RedeemedVoucherRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class AlreadyUsedVoucherConsumer implements VoucherConsumerInterface
{
    private RedeemedVoucherRepository $redeemedVoucherRepository;

    public function __construct(RedeemedVoucherRepository $redeemedVoucherRepository)
    {
        $this->redeemedVoucherRepository = $redeemedVoucherRepository;
    }

    public static function getPriority(): int
    {
        // Executed after defaults one because it induces a db query.
        return 80;
    }

    public function supports(Voucher $voucher, UserInterface $user): bool
    {
        return true;
    }

    public function handle(Voucher $voucher, UserInterface $user): void
    {
        $similar = $this->redeemedVoucherRepository->findByVoucherAndUser($voucher, $user);

        if (\count($similar) > 0) {
            throw new UserHasAlreadyRedeemedThisVoucher($voucher, $user);
        }
    }
}
