<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Supplement;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Supplement\Supplement;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Supplement::class)]
final class SupplementTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Supplement::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 1, 'lyrics' => ['id' => 2, 'lyrics' => 'a', 'fullLyrics' => 'b', 'hasRights' => true, 'showTranslation' => false], 'videos' => [['cover' => 'c', 'provider' => 'youtube']], 'radioIsAvailable' => true, 'description' => 'Описание эпизода'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 1];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Supplement::class, $model);
        self::assertSame(1, $model->id);
        self::assertSame('b', $model->lyrics?->fullLyrics);
        self::assertCount(1, $model->videos);
        self::assertSame('youtube', $model->videos[0]->provider);
        self::assertSame('Описание эпизода', $model->description);
    }

    protected function equalityTriple(): array
    {
        return [new Supplement(1), new Supplement(1), new Supplement(2)];
    }
}
