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

namespace Tests\Orbitale\Vouch\Entity;

use Orbitale\Vouch\Entity\Voucher;
use PHPUnit\Framework\TestCase;

class VoucherTest extends TestCase
{
    public function test voucher to string returns unique code(): void
    {
        $code = \uniqid('', true);

        $voucher = Voucher::create(
            'some_voucher_type',
            $code,
            new \DateTimeImmutable()
        );

        static::assertSame($code, $voucher->toString());
    }
}
