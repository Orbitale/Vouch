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

namespace Orbitale\Vouch\Exception;

class VoucherNotAvailable extends RedeemException
{
    public function redeemErrorMessage(): string
    {
        return 'voucher.redeem.error.not_available';
    }
}
