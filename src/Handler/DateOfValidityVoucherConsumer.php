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
use Orbitale\Vouch\Exception\VoucherNotAvailable;
use Symfony\Component\Security\Core\User\UserInterface;

class DateOfValidityVoucherConsumer implements VoucherConsumerInterface
{
    public static function getPriority(): int
    {
        return 100;
    }

    public function supports(Voucher $voucher, UserInterface $user): bool
    {
        return true;
    }

    public function handle(Voucher $voucher, UserInterface $user): void
    {
        $now = new \DateTimeImmutable();

        if (
            ($voucher->getValidFrom() && $now < $voucher->getValidFrom())
            ||
            ($voucher->getValidUntil() && $now > $voucher->getValidUntil())
        ) {
            throw new VoucherNotAvailable($voucher, $user);
        }
    }
}
