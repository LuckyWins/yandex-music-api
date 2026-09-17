<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Disclaimer;
use LuckyWins\YandexMusic\Model\ForeignAgent;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Disclaimer::class)]
final class DisclaimerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Disclaimer::class;
    }

    protected static function fullPayload(): array
    {
        return ['foreignAgent' => ['reason' => 'решение минюста', 'title' => 'Иностранный агент']];
    }

    protected static function requiredPayload(): array
    {
        return ['foreignAgent' => ['title' => 'x']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Disclaimer::class, $model);
        self::assertSame('Иностранный агент', $model->foreignAgent?->title);
    }

    protected function equalityTriple(): array
    {
        return [new Disclaimer(), new Disclaimer(), new Disclaimer(new ForeignAgent('r'))];
    }
}
