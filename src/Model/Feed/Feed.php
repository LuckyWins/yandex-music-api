<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;

/**
 * The account's feed: what the service made for it, and what happened on
 * which day.
 */
final class Feed extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'generatedPlaylists' => [GeneratedPlaylist::class, 'list'],
        'days' => [Day::class, 'list'],
    ];

    public function __construct(
        public readonly ?bool $canGetMoreEvents = null,
        public readonly ?bool $pumpkin = null,
        public readonly ?bool $isWizardPassed = null,
        /** @var list<GeneratedPlaylist> */
        public readonly array $generatedPlaylists = [],
        /** @var list<string> */
        public readonly array $headlines = [],
        public readonly ?string $today = null,
        /** @var list<Day> */
        public readonly array $days = [],
        public readonly ?string $nextRevision = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->today, $this->nextRevision];
    }
}
