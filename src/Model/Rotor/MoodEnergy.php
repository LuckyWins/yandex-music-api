<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

/**
 * The mood a station is asked for.
 *
 * Not every station advertises this one in its Restrictions, but the endpoint
 * accepts it for all of them.
 */
enum MoodEnergy: string
{
    case All = 'all';
    case Sad = 'sad';
    case Calm = 'calm';
    case Active = 'active';
    case Fun = 'fun';
}
