<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A setting that takes one of a listed set of values.
 *
 * This is how the service says what a station accepts, rather than leaving it
 * to be guessed — which is why the enums in this library are matched against
 * these lists rather than being the only thing that can be sent.
 *
 * Named after the reference library's class. `Enum` is a legal class name in
 * PHP despite `enum` being a language keyword.
 */
final class Enum extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'possibleValues' => [Value::class, 'list'],
    ];

    public function __construct(
        public readonly string $type,
        public readonly string $name,
        /** @var list<Value> */
        public readonly array $possibleValues = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Just the values, without their display names.
     *
     * @return list<string>
     */
    public function values(): array
    {
        return array_map(static fn (Value $value): string => $value->value, $this->possibleValues);
    }

    protected function identity(): array
    {
        return [$this->type, $this->name];
    }
}
