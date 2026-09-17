<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Link;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Link::class)]
final class LinkTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Link::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Официальный сайт', 'href' => 'https://example.invalid', 'type' => 'official', 'socialNetwork' => 'vk'];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Официальный сайт', 'href' => 'https://example.invalid', 'type' => 'official'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Link::class, $model);
        self::assertSame('Официальный сайт', $model->title);
        self::assertSame('official', $model->type);
        self::assertSame('vk', $model->socialNetwork);
    }

    protected function equalityTriple(): array
    {
        return [new Link('t', 'h', 'official'), new Link('t', 'h', 'official'), new Link('t', 'h', 'social')];
    }
}
