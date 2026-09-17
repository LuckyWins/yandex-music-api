<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One run of text in a licence notice, optionally a link.
 */
final class LicenceTextPart extends Model
{
    public function __construct(
        public readonly string $text,
        public readonly ?string $url = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->text];
    }
}
