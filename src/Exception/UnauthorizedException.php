<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Exception;

/**
 * HTTP 401 or 403. The token is missing, expired, or lacks the required rights.
 */
class UnauthorizedException extends YandexMusicException
{
}
