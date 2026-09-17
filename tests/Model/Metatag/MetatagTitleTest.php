<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Metatag\MetatagTitle;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetatagTitle::class)]
final class MetatagTitleTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetatagTitle::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Для бега', 'fullTitle' => 'Музыка для бега'];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Для бега'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetatagTitle::class, $model);
        self::assertSame('Для бега', $model->title);
        self::assertSame('Музыка для бега', $model->fullTitle);
    }

    protected function equalityTriple(): array
    {
        return [new MetatagTitle('Для бега'), new MetatagTitle('Для бега'), new MetatagTitle('Для сна')];
    }
}
