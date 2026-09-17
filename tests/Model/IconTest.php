<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Icon;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Icon::class)]
final class IconTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Icon::class;
    }

    protected static function fullPayload(): array
    {
        return ['backgroundColor' => '#ff0000', 'imageUrl' => 'avatars.invalid/icon/%%'];
    }

    protected static function requiredPayload(): array
    {
        return ['imageUrl' => 'avatars.invalid/icon/%%'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Icon::class, $model);
        self::assertSame('#ff0000', $model->backgroundColor);
        self::assertSame('avatars.invalid/icon/%%', $model->imageUrl);
        self::assertSame('https://avatars.invalid/icon/300x300', $model->url('300x300'));
    }

    public function testNoImageMeansNoUrl(): void
    {
        $model = Icon::fromApi(['backgroundColor' => '#ff0000'], self::client());

        self::assertInstanceOf(Icon::class, $model);
        self::assertNull($model->url());
    }

    protected function equalityTriple(): array
    {
        return [
            new Icon('#ff0000', 'a/%%'),
            new Icon('#ff0000', 'a/%%'),
            new Icon('#ff0000', 'b/%%'),
        ];
    }
}
