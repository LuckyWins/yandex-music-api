<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

/**
 * How far a station wanders from what is already known to be liked.
 *
 * Taken from the service rather than from a station's Restrictions, which
 * advertise `diverse` — a value the endpoint then refuses. Checked against
 * the live API.
 */
enum Diversity: string
{
    case Default = 'default';
    case Discover = 'discover';
    case Favorite = 'favorite';
    case Popular = 'popular';
}
