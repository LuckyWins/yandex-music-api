<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Label;

use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Label::class)]
final class LabelTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Label::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 5, 'name' => 'Hajime Records', 'description' => 'Лейбл', 'image' => 'https://example.invalid/i.png', 'type' => 'major', 'links' => [['title' => 'Сайт', 'href' => 'https://example.invalid', 'type' => 'official']]];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 5, 'name' => 'Hajime Records'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Label::class, $model);
        self::assertSame(5, $model->id);
        self::assertSame('Hajime Records', $model->name);
        self::assertCount(1, $model->links ?? []);
        $link = ($model->links ?? [])[0] ?? null;
        self::assertNotNull($link);
        self::assertSame('official', $link->type);
    }

    protected function equalityTriple(): array
    {
        return [new Label(5, 'a'), new Label(5, 'a'), new Label(6, 'a')];
    }
}
