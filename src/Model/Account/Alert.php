<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A banner the app is asked to show, such as a subscription prompt.
 *
 * Experimental on Yandex's side: identifiers come back as literal `xxx` and the
 * text is sometimes a placeholder, so do not treat `alertId` as unique.
 */
final class Alert extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'button' => [AlertButton::class, 'one'],
    ];

    public function __construct(
        public readonly string $alertId,
        public readonly string $text,
        public readonly string $bgColor,
        public readonly string $textColor,
        public readonly string $alertType,
        public readonly ?AlertButton $button = null,
        public readonly ?bool $closeButton = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->alertId];
    }
}
