<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\PersonalPlaylistsData;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PersonalPlaylistsData::class)]
final class PersonalPlaylistsDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PersonalPlaylistsData::class;
    }

    protected static function fullPayload(): array
    {
        return ['isWizardPassed' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['isWizardPassed' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PersonalPlaylistsData::class, $model);
        self::assertTrue($model->isWizardPassed);
    }

    protected function equalityTriple(): array
    {
        return [new PersonalPlaylistsData(true), new PersonalPlaylistsData(true), new PersonalPlaylistsData(false)];
    }
}
