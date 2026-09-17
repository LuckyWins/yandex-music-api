<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Wave;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\WaveAgentEntity;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(WaveAgentEntity::class)]
final class WaveAgentEntityTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return WaveAgentEntity::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'artist'];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'artist'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(WaveAgentEntity::class, $model);
        self::assertSame('artist', $model->type);
    }

    protected function equalityTriple(): array
    {
        return [new WaveAgentEntity('artist'), new WaveAgentEntity('artist'), new WaveAgentEntity('playlist')];
    }
}
