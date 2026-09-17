<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\BlockType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The wire values decide what the front page answers with, so they are pinned
 * here: three different spellings live side by side, and none of them is ours
 * to tidy.
 */
#[CoversClass(BlockType::class)]
final class BlockTypeTest extends TestCase
{
    public function testTheWireValues(): void
    {
        self::assertSame('personalplaylists', BlockType::PersonalPlaylists->value);
        self::assertSame('promotions', BlockType::Promotions->value);
        self::assertSame('new-releases', BlockType::NewReleases->value);
        self::assertSame('new-playlists', BlockType::NewPlaylists->value);
        self::assertSame('mixes', BlockType::Mixes->value);
        self::assertSame('chart', BlockType::Chart->value);
        self::assertSame('artists', BlockType::Artists->value);
        self::assertSame('albums', BlockType::Albums->value);
        self::assertSame('playlists', BlockType::Playlists->value);
        self::assertSame('play_contexts', BlockType::PlayContexts->value);
        self::assertCount(10, BlockType::cases());
    }
}
