<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Rotor\Diversity;
use LuckyWins\YandexMusic\Model\Rotor\FeedbackType;
use LuckyWins\YandexMusic\Model\Rotor\MoodEnergy;
use LuckyWins\YandexMusic\Model\Rotor\StationLanguage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The wire values are the service's own, taken from what it answers when
 * asked to accept an impossible one. Renaming a case would change what gets
 * sent, so they are pinned here.
 */
#[CoversClass(FeedbackType::class)]
#[CoversClass(StationLanguage::class)]
#[CoversClass(Diversity::class)]
#[CoversClass(MoodEnergy::class)]
final class VocabulariesTest extends TestCase
{
    public function testFeedbackTypes(): void
    {
        self::assertSame('radioStarted', FeedbackType::RadioStarted->value);
        self::assertSame('trackStarted', FeedbackType::TrackStarted->value);
        self::assertSame('trackFinished', FeedbackType::TrackFinished->value);
        self::assertSame('skip', FeedbackType::Skip->value);
        self::assertCount(4, FeedbackType::cases());
    }

    public function testLanguages(): void
    {
        self::assertSame(
            ['russian', 'not-russian', 'without-words', 'any'],
            array_map(static fn (StationLanguage $case): string => $case->value, StationLanguage::cases()),
        );
    }

    /**
     * `discover`, not `diverse`: the stations advertise the latter and the
     * endpoint refuses it.
     */
    public function testDiversities(): void
    {
        self::assertSame(
            ['default', 'discover', 'favorite', 'popular'],
            array_map(static fn (Diversity $case): string => $case->value, Diversity::cases()),
        );
        self::assertNull(Diversity::tryFrom('diverse'));
    }

    public function testMoods(): void
    {
        self::assertSame(
            ['all', 'sad', 'calm', 'active', 'fun'],
            array_map(static fn (MoodEnergy $case): string => $case->value, MoodEnergy::cases()),
        );
    }
}
