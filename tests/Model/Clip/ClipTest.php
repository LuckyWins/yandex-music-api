<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Clip;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Clip::class)]
final class ClipTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Clip::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'clipId' => 91,
            'title' => 'Нирвана',
            'version' => 'official video',
            'playerId' => 'vh-player',
            'uuid' => 'a9f0c1e2-0000-4000-8000-000000000002',
            'thumbnail' => 'avatars.invalid/thumb/%%',
            'previewUrl' => 'https://avatars.invalid/preview.mp4',
            'duration' => 213,
            'trackIds' => [31190260, 31190261],
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'disclaimers' => ['modified'],
            'explicit' => true,
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
            'contentRestrictions' => ['available' => true],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['clipId' => 91];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Clip::class, $model);
        self::assertSame(91, $model->clipId);
        self::assertSame('Нирвана', $model->title);
        self::assertSame('official video', $model->version);
        self::assertSame('vh-player', $model->playerId);
        self::assertSame('a9f0c1e2-0000-4000-8000-000000000002', $model->uuid);
        self::assertSame(213, $model->duration);
        self::assertSame([31190260, 31190261], $model->trackIds);
        self::assertCount(1, $model->artists);
        self::assertInstanceOf(Artist::class, $model->artists[0]);
        self::assertSame(['modified'], $model->disclaimers);
        self::assertTrue($model->explicit);
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertInstanceOf(ContentRestrictions::class, $model->contentRestrictions);
    }

    protected function equalityTriple(): array
    {
        return [new Clip(91), new Clip(91, 'другое название'), new Clip(92)];
    }
}
