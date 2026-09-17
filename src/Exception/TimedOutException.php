<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Exception;

/**
 * The request did not complete within the configured timeout.
 */
class TimedOutException extends NetworkException
{
}
