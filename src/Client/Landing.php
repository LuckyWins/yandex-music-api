<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Feed\Feed;
use LuckyWins\YandexMusic\Model\Genre\Genre;
use LuckyWins\YandexMusic\Model\Landing\BlockType;
use LuckyWins\YandexMusic\Model\Landing\ChartInfo;
use LuckyWins\YandexMusic\Model\Landing\Landing as LandingPage;
use LuckyWins\YandexMusic\Model\Landing\LandingList;
use LuckyWins\YandexMusic\Model\Playlist\TagResult;

/**
 * The front page: the landing blocks, the feed, the charts and the genres.
 */
trait Landing
{
    /**
     * The account's feed.
     */
    public function feed(): ?Feed
    {
        return Feed::fromApi($this->request->get($this->getBaseUrl().'/feed'), $this);
    }

    /**
     * Whether the account has answered the taste wizard, which is what makes
     * its personal playlists worth anything.
     */
    public function feedWizardIsPassed(): bool
    {
        $result = $this->request->get($this->getBaseUrl().'/feed/wizard/is-passed');

        return is_array($result) && true === ($result['isWizardPassed'] ?? null);
    }

    /**
     * The front page, one block at a time or several at once.
     *
     * Blocks are named by BlockType; a bare string works too, for a block
     * this library has not heard of.
     *
     * @param BlockType|string|list<BlockType|string> $blocks
     * @param string|null                             $eitherUserId whose front page to ask for, when not the
     *                                                              account's own. The reference library pins a
     *                                                              stranger's id here on every request; this does
     *                                                              not send one unless asked.
     */
    public function landing(BlockType|string|array $blocks, ?string $eitherUserId = null): ?LandingPage
    {
        $wanted = array_map(
            static fn (BlockType|string $block): string => $block instanceof BlockType ? $block->value : $block,
            is_array($blocks) ? $blocks : [$blocks],
        );

        $params = ['blocks' => implode(',', $wanted)];

        if (null !== $eitherUserId) {
            $params['eitherUserId'] = $eitherUserId;
        }

        return LandingPage::fromApi($this->request->get($this->getBaseUrl().'/landing3', $params), $this);
    }

    /**
     * A chart.
     *
     * @param string|null $chartOption which chart, from the menu of the one
     *                                 already fetched — `russia`, `world`
     */
    public function chart(?string $chartOption = null): ?ChartInfo
    {
        $url = $this->getBaseUrl().'/landing3/chart';

        if (null !== $chartOption && '' !== $chartOption) {
            $url .= '/'.$chartOption;
        }

        return ChartInfo::fromApi($this->request->get($url), $this);
    }

    /**
     * Newly released albums, as ids.
     */
    public function newReleases(): ?LandingList
    {
        return LandingList::fromApi($this->request->get($this->getBaseUrl().'/landing3/new-releases'), $this);
    }

    /**
     * Newly published playlists, as owner-and-kind pairs.
     */
    public function newPlaylists(): ?LandingList
    {
        return LandingList::fromApi($this->request->get($this->getBaseUrl().'/landing3/new-playlists'), $this);
    }

    /**
     * Podcasts, as album ids — a podcast is an album and its episodes are
     * tracks.
     */
    public function podcasts(): ?LandingList
    {
        return LandingList::fromApi($this->request->get($this->getBaseUrl().'/landing3/podcasts'), $this);
    }

    /**
     * Every genre, each carrying its own sub-genres.
     *
     * @return list<Genre>
     */
    public function genres(): array
    {
        return Genre::listFromApi($this->request->get($this->getBaseUrl().'/genres'), $this);
    }

    /**
     * The playlists filed under a tag.
     */
    public function tags(string $tagId): ?TagResult
    {
        return TagResult::fromApi(
            $this->request->get($this->getBaseUrl().'/tags/'.$tagId.'/playlist-ids'),
            $this,
        );
    }
}
