<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Genre;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A genre's artwork, in the two sizes the service offers.
 *
 * The properties are named for the keys that carry them. A PHP name cannot
 * begin with a digit, hence the underscore — and the key matching, which
 * ignores separators, lines `$_208x208` up with `208x208` from the response
 * without any help.
 */
final class Images extends Model
{
    public function __construct(
        public readonly ?string $_20x20 = null,
        public readonly ?string $_208x208 = null,
        public readonly ?string $_300x300 = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->_208x208, $this->_300x300];
    }
}
