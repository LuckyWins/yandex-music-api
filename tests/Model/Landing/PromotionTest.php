<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\Promotion;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Promotion::class)]
final class PromotionTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Promotion::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'promoId' => 'p1',
            'title' => 'Новый альбом',
            'subtitle' => 'Уже вышел',
            'heading' => 'Премьера',
            'url' => '/album/4243617',
            'urlScheme' => 'yandexmusic://album/4243617',
            'textColor' => '#ffffff',
            'gradient' => 'linear-gradient(#000, #fff)',
            'image' => 'avatars.invalid/promo/%%',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['promoId' => 'p1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Promotion::class, $model);
        self::assertSame('p1', $model->promoId);
        self::assertSame('Новый альбом', $model->title);
        self::assertSame('Уже вышел', $model->subtitle);
        self::assertSame('Премьера', $model->heading);
        self::assertSame('/album/4243617', $model->url);
        self::assertSame('yandexmusic://album/4243617', $model->urlScheme);
        self::assertSame('#ffffff', $model->textColor);
        self::assertSame('linear-gradient(#000, #fff)', $model->gradient);
        self::assertSame('avatars.invalid/promo/%%', $model->image);
    }

    protected function equalityTriple(): array
    {
        return [new Promotion('p1'), new Promotion('p1', 'иначе'), new Promotion('p2')];
    }
}
