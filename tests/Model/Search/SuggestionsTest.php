<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Search;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Search\Best;
use LuckyWins\YandexMusic\Model\Search\Suggestions;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Suggestions::class)]
final class SuggestionsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Suggestions::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'best' => ['type' => 'track', 'result' => ['id' => 31190260, 'title' => 'Нирвана']],
            'suggestions' => ['нирвана', 'нирвана miyagi', 'nirvana'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['suggestions' => ['нирвана']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Suggestions::class, $model);
        self::assertInstanceOf(Best::class, $model->best);
        self::assertInstanceOf(Track::class, $model->best->result);
        self::assertSame(['нирвана', 'нирвана miyagi', 'nirvana'], $model->suggestions);
    }

    protected function equalityTriple(): array
    {
        return [
            new Suggestions(null, ['нирвана']),
            new Suggestions(null, ['нирвана']),
            new Suggestions(null, ['nirvana']),
        ];
    }
}
