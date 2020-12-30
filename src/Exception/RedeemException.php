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

use Orbitale\Vouch\Entity\Voucher;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class RedeemException extends \RuntimeException implements RedeemExceptionInterface
{
    private $voucher;
    private $user;

    public function __construct(Voucher $voucher, UserInterface $user, \Throwable $previous = null)
    {
        $this->voucher = $voucher;
        $this->user = $user;
        parent::__construct(\strtr($this->redeemErrorMessage(), $this->getMessageTranslationParameters()), 0, $previous);
    }

    public function getMessageTranslationParameters(): array
    {
        return [
            '%username%' => $this->user->getUsername(),
            '%voucher_type%' => $this->voucher->getType(),
            '%voucher_code%' => $this->voucher->getUniqueCode(),
        ];
    }
}
