<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\ForeignAgent;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ForeignAgent::class)]
final class ForeignAgentTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ForeignAgent::class;
    }

    protected static function fullPayload(): array
    {
        return ['reason' => 'решение минюста', 'title' => 'Иностранный агент'];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['reason' => 'решение минюста'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ForeignAgent::class, $model);
        self::assertSame('Иностранный агент', $model->title);
    }

    protected function equalityTriple(): array
    {
        return [new ForeignAgent('r', 't'), new ForeignAgent('r', 't'), new ForeignAgent('r', 'u')];
    }
}
