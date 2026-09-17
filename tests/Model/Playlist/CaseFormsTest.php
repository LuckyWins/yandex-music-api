<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\CaseForms;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CaseForms::class)]
final class CaseFormsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return CaseForms::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'nominative' => 'Андрей',
            'genitive' => 'Андрея',
            'dative' => 'Андрею',
            'accusative' => 'Андрея',
            'instrumental' => 'Андреем',
            'prepositional' => 'Андрее',
        ];
    }

    protected static function requiredPayload(): array
    {
        return self::fullPayload();
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(CaseForms::class, $model);
        self::assertSame('Андрей', $model->nominative);
        self::assertSame('Андрея', $model->genitive);
        self::assertSame('Андрею', $model->dative);
        self::assertSame('Андрея', $model->accusative);
        self::assertSame('Андреем', $model->instrumental);
        self::assertSame('Андрее', $model->prepositional);
    }

    protected function equalityTriple(): array
    {
        $forms = static fn (string $name): CaseForms => new CaseForms($name, $name, $name, $name, $name, $name);

        return [$forms('Андрей'), $forms('Андрей'), $forms('Пётр')];
    }
}
