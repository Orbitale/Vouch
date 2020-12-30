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

namespace Tests\Orbitale\Vouch\Redeem;

use Orbitale\Vouch\Entity\Voucher;
use Orbitale\Vouch\Exception\NoConsumerForVoucherAndUser;
use Orbitale\Vouch\Handler\VoucherConsumerInterface;
use Orbitale\Vouch\Redeem\Redeemer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\User\UserInterface;

class RedeemerTest extends TestCase
{
    public function test redeem with no handlers throws exception(): void
    {
        $redeemer = $this->createRedeemer([]);

        $this->expectException(NoConsumerForVoucherAndUser::class);
        $this->expectExceptionMessage('voucher.redeem.error.no_handler');

        $redeemer->redeem($this->createVoucher(), $this->createUser());
    }

    public function test redeem with handler stopping propagation(): void
    {
        $voucher = $this->createVoucher();
        $user = $this->createUser();

        $handler = $this->createMock(VoucherConsumerInterface::class);

        $handler->expects(static::once())
            ->method('supports')
            ->with($voucher, $user)
            ->willReturn(true)
        ;

        $handler->expects(static::once())
            ->method('handle')
            ->with($voucher, $user)
        ;

        $return = $this->createRedeemer([$handler])->redeem($voucher, $user);

        static::assertSame([\get_class($handler)], $return);
    }

    public function test redeem with handler logs message(): void
    {
        $handler = $this->createMock(VoucherConsumerInterface::class);
        $voucher = $this->createVoucher();
        $user = $this->createUser();

        $handler->expects(static::once())
            ->method('supports')
            ->with($voucher, $user)
            ->willReturn(true)
        ;
        $handler->expects(static::once())
            ->method('handle')
            ->with($voucher, $user)
        ;

        $user->expects(static::once())
            ->method('getUsername')
            ->willReturn('test_user')
        ;

        $voucher->expects(static::once())
            ->method('toString')
            ->willReturn('unique_code')
        ;

        $logger = new class() extends AbstractLogger {
            public array $logs = [];

            public function log($level, $message, array $context = []): void
            {
                $this->logs[] = [$level, $message, $context];
            }
        };

        $this->createRedeemer([$handler], $logger)->redeem($voucher, $user);

        static::assertSame([
            [
                'info',
                'Redeemed voucher',
                [
                    'voucher' => 'unique_code',
                    'user' => 'test_user',
                    'consumers' => [\get_class($handler)],
                ],
            ],
        ], $logger->logs);
    }

    /**
     * @return MockObject|Voucher
     */
    private function createVoucher(): Voucher
    {
        $voucher = $this->createMock(Voucher::class);
        $voucher
            ->method('getType')
            ->willReturn('test_type')
        ;

        return $voucher;
    }

    /**
     * @return MockObject|UserInterface
     */
    private function createUser(): UserInterface
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->method('getUsername')
            ->willReturn('test_user')
        ;

        return $user;
    }

    private function createRedeemer(array $handlers, LoggerInterface $logger = null): Redeemer
    {
        if (!$logger) {
            $logger = new NullLogger();
        }

        return new Redeemer($handlers, $logger);
    }
}
