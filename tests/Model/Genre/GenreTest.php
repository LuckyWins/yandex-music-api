<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Genre;

use LuckyWins\YandexMusic\Model\Genre\Genre;
use LuckyWins\YandexMusic\Model\Genre\Images;
use LuckyWins\YandexMusic\Model\Genre\Title;
use LuckyWins\YandexMusic\Model\Icon;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Genre::class)]
final class GenreTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Genre::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'rock',
            'weight' => 10,
            'composerTop' => false,
            'title' => 'Рок',
            'fullTitle' => 'Рок-музыка',
            'showInMenu' => true,
            'showInRegions' => [225, 149],
            'hideInRegions' => [181],
            'urlPart' => 'rock',
            'color' => '#ff0000',
            'titles' => [
                'ru' => ['title' => 'Рок', 'fullTitle' => 'Рок-музыка'],
                'en' => ['title' => 'Rock'],
            ],
            'images' => ['20x20' => 'https://avatars.invalid/20.jpg', '208x208' => 'https://avatars.invalid/208.jpg'],
            'radioIcon' => ['backgroundColor' => '#ff0000', 'imageUrl' => 'avatars.invalid/icon/%%'],
            'subGenres' => [
                ['id' => 'punk', 'title' => 'Панк', 'titles' => ['en' => ['title' => 'Punk']]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'rock'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Genre::class, $model);
        self::assertSame('rock', $model->id);
        self::assertSame('Рок', $model->title);
        self::assertSame('Рок-музыка', $model->fullTitle);
        self::assertSame(10, $model->weight);
        self::assertFalse($model->composerTop);
        self::assertTrue($model->showInMenu);
        self::assertSame([225, 149], $model->showInRegions);
        self::assertSame([181], $model->hideInRegions);
        self::assertSame('rock', $model->urlPart);
        self::assertSame('#ff0000', $model->color);
        self::assertInstanceOf(Images::class, $model->images);
        self::assertInstanceOf(Icon::class, $model->radioIcon);
    }

    /**
     * The titles arrive keyed by language rather than as a list, which is what
     * the base class's map support is for.
     */
    public function testTitlesAreKeyedByLanguage(): void
    {
        $model = Genre::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Genre::class, $model);
        self::assertSame(['ru', 'en'], array_keys($model->titles));
        self::assertInstanceOf(Title::class, $model->titles['en']);
        self::assertSame('Rock', $model->titles['en']->title);
        self::assertSame('Рок-музыка', $model->titles['ru']->fullTitle);
    }

    public function testATitleFallsBackToTheDefault(): void
    {
        $model = Genre::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Genre::class, $model);
        self::assertSame('Rock', $model->titleIn('en'));
        self::assertSame('Рок', $model->titleIn('kk'), 'no such language, so the genre-s own title');
    }

    public function testSubGenresAreGenres(): void
    {
        $model = Genre::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Genre::class, $model);
        self::assertCount(1, $model->subGenres);
        self::assertSame('punk', $model->subGenres[0]->id);
        self::assertSame('Punk', $model->subGenres[0]->titleIn('en'));
        self::assertSame([], $model->subGenres[0]->subGenres);
    }

    protected function equalityTriple(): array
    {
        return [new Genre('rock'), new Genre('rock', 'Рок'), new Genre('rap')];
    }
}
