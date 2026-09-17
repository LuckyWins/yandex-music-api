<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Credit;
use LuckyWins\YandexMusic\Model\Credits;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Credits::class)]
final class CreditsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Credits::class;
    }

    protected static function fullPayload(): array
    {
        return ['credits' => [['title' => 'Продюсер', 'value' => 'Кто-то'], ['title' => 'Сведение', 'value' => 'Другой']]];
    }

    protected static function requiredPayload(): array
    {
        return ['credits' => []];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Credits::class, $model);
        self::assertCount(2, $model->credits);
        self::assertSame('Продюсер', $model->credits[0]->title);
    }

    protected function equalityTriple(): array
    {
        return [new Credits(), new Credits(), new Credits([new Credit('a')])];
    }
}
