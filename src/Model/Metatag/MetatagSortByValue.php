<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One way a tag's contents can be ordered, and whether it is the one in use.
 *
 * The service advertises these rather than leaving them to be guessed, the
 * way a radio station advertises its settings.
 */
final class MetatagSortByValue extends Model
{
    public function __construct(
        public readonly ?string $value = null,
        public readonly ?string $title = null,
        public readonly ?bool $active = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->value];
    }
}
