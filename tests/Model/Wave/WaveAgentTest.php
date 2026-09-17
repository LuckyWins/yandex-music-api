<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Wave;

use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\WaveAgent;
use LuckyWins\YandexMusic\Model\Wave\WaveAgentEntity;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(WaveAgent::class)]
final class WaveAgentTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return WaveAgent::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'animationUri' => 'https://avatars.invalid/animation.json',
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
            'entity' => ['type' => 'artist'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['animationUri' => 'https://avatars.invalid/animation.json'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(WaveAgent::class, $model);
        self::assertSame('https://avatars.invalid/animation.json', $model->animationUri);
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertInstanceOf(WaveAgentEntity::class, $model->entity);
        self::assertSame('artist', $model->entity->type);
    }

    protected function equalityTriple(): array
    {
        return [
            new WaveAgent('https://a.invalid/1.json', entity: new WaveAgentEntity('artist')),
            new WaveAgent('https://a.invalid/1.json', entity: new WaveAgentEntity('artist')),
            new WaveAgent('https://a.invalid/1.json', entity: new WaveAgentEntity('playlist')),
        ];
    }
}
