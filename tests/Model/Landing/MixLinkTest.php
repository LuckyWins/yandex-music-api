<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\MixLink;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MixLink::class)]
final class MixLinkTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MixLink::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'title' => 'Рок',
            'url' => '/tag/rock',
            'urlScheme' => 'yandexmusic://tag/rock',
            'textColor' => '#ffffff',
            'backgroundColor' => '#ff0000',
            'backgroundImageUri' => 'avatars.invalid/bg/%%',
            'coverWhite' => 'avatars.invalid/white/%%',
            'coverUri' => 'avatars.invalid/cover/%%',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Рок'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MixLink::class, $model);
        self::assertSame('Рок', $model->title);
        self::assertSame('/tag/rock', $model->url);
        self::assertSame('yandexmusic://tag/rock', $model->urlScheme);
        self::assertSame('#ffffff', $model->textColor);
        self::assertSame('#ff0000', $model->backgroundColor);
        self::assertSame('avatars.invalid/bg/%%', $model->backgroundImageUri);
        self::assertSame('avatars.invalid/white/%%', $model->coverWhite);
        self::assertSame('avatars.invalid/cover/%%', $model->coverUri);
    }

    protected function equalityTriple(): array
    {
        return [new MixLink('Рок', '/tag/rock'), new MixLink('Рок', '/tag/rock'), new MixLink('Рэп', '/tag/rap')];
    }
}
