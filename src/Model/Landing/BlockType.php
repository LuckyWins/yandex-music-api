<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

/**
 * The blocks the front page can be asked for.
 *
 * Spelling one wrong is answered by leaving it out of the response rather than
 * by an error, which is why these are an enum. A bare string is still accepted
 * everywhere one of these is, so a block the service adds later needs no
 * release here.
 */
enum BlockType: string
{
    case PersonalPlaylists = 'personalplaylists';
    case Promotions = 'promotions';
    case NewReleases = 'new-releases';
    case NewPlaylists = 'new-playlists';
    case Mixes = 'mixes';
    case Chart = 'chart';
    case Artists = 'artists';
    case Albums = 'albums';
    case Playlists = 'playlists';
    case PlayContexts = 'play_contexts';
}
