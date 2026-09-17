<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

/**
 * What a station is being told.
 *
 * These four are fixed by the protocol rather than advertised by the service,
 * unlike the station settings.
 */
enum FeedbackType: string
{
    case RadioStarted = 'radioStarted';
    case TrackStarted = 'trackStarted';
    case TrackFinished = 'trackFinished';
    case Skip = 'skip';
}
