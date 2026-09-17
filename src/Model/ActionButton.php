<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * A call to action shown on an album or a playlist, such as a pre-save
 * prompt or a link to a promotion.
 */
final class ActionButton extends Model
{
    public function __construct(
        public readonly ?string $text = null,
        public readonly ?string $url = null,
        public readonly ?string $color = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->text, $this->url, $this->color];
    }
}
