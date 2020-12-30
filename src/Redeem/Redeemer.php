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

namespace Orbitale\Vouch\Redeem;

use Orbitale\Vouch\Entity\Voucher;
use Orbitale\Vouch\Exception\NoConsumerForVoucherAndUser;
use Orbitale\Vouch\Exception\RedeemExceptionInterface;
use Orbitale\Vouch\Exception\StopRedeemPropagation;
use Orbitale\Vouch\Handler\VoucherConsumerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class Redeemer
{
    /**
     * @var array<VoucherConsumerInterface>
     */
    private array $consumers = [];

    private LoggerInterface $logger;

    public function __construct(iterable $consumers, LoggerInterface $logger)
    {
        foreach ($consumers as $consumer) {
            $this->addConsumer($consumer);
        }

        $this->sortConsumers();
        $this->logger = $logger;
    }

    /**
     * @throws RedeemExceptionInterface
     *
     * @return string[] the names of consumers that were executed
     */
    public function redeem(Voucher $voucher, UserInterface $user): array
    {
        $handled = [];

        try {
            foreach ($this->consumers as $consumer) {
                if ($consumer->supports($voucher, $user)) {
                    $handled[] = \get_class($consumer);
                    $consumer->handle($voucher, $user);
                }
            }
        } catch (StopRedeemPropagation $e) {
            // Stop silently
        }

        if (!$handled) {
            throw new NoConsumerForVoucherAndUser($voucher, $user);
        }

        $this->logger->info('Redeemed voucher', [
            'voucher' => $voucher->toString(),
            'user' => $user->getUsername(),
            'consumers' => $handled,
        ]);

        return $handled;
    }

    private function addConsumer(VoucherConsumerInterface $consumer): void
    {
        $this->consumers[] = $consumer;
    }

    private function sortConsumers(): void
    {
        \usort($this->consumers, static function (
            VoucherConsumerInterface $consumer1,
            VoucherConsumerInterface $consumer2
        ) {
            return $consumer2::getPriority() <=> $consumer1::getPriority();
        });
    }
}
