<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Cover::class)]
final class CoverTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Cover::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'pic', 'uri' => 'avatars.yandex.net/get-music-content/1/%%', 'prefix' => 'abc', 'version' => '1', 'itemsUri' => ['a', 'b'], 'derivedColors' => ['average' => '#112233']];
    }

    protected static function requiredPayload(): array
    {
        return ['uri' => 'avatars.yandex.net/get-music-content/1/%%'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Cover::class, $model);
        self::assertSame('pic', $model->type);
        self::assertSame(['a', 'b'], $model->itemsUri);
        self::assertSame('#112233', $model->derivedColors?->average);
    }

    protected function equalityTriple(): array
    {
        return [new Cover(uri: 'u', prefix: 'p'), new Cover(uri: 'u', prefix: 'p'), new Cover(uri: 'v', prefix: 'p')];
    }

    /**
     * Covers are templates, not URLs: the size goes where the %% is.
     */
    public function testUrlFillsInTheSize(): void
    {
        $model = Cover::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Cover::class, $model);
        self::assertSame('https://avatars.yandex.net/get-music-content/1/400x400', $model->url('400x400'));
        self::assertNull(Cover::fromApi(['type' => 'pic'], self::client())?->url());
    }
}
