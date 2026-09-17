<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

/**
 * What a station may sing in.
 *
 * The cases are the values the service itself named when asked to accept an
 * impossible one; a station's own Restrictions advertise the same four. Pass
 * a bare string instead when the service grows a fifth.
 */
enum StationLanguage: string
{
    case Russian = 'russian';
    case NotRussian = 'not-russian';
    case WithoutWords = 'without-words';
    case Any = 'any';
}
