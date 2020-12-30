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
use Orbitale\Vouch\Exception\RedeemException;
use Orbitale\Vouch\Exception\StopRedeemPropagation;
use Symfony\Component\Security\Core\User\UserInterface;

interface VoucherConsumerInterface
{
    /**
     * Used to specify the order in which handlers are checked.
     * The higher priority, the sooner it handles the voucher.
     */
    public static function getPriority(): int;

    /**
     * Silently checks if this handler can be used for this voucher and user.
     * Should not throw exception.
     */
    public function supports(Voucher $voucher, UserInterface $user): bool;

    /**
     * Executes an action for this voucher to be redeemed.
     * May throw an exception if redeem failed.
     *
     * @throws RedeemException
     * @throws StopRedeemPropagation to prevent executing next handlers
     */
    public function handle(Voucher $voucher, UserInterface $user): void;
}
