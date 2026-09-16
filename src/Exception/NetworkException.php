<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Exception;

/**
 * Transport-level failure, or an API error not covered by a more specific class.
 *
 * Also used for HTTP 409, 413 and 502, and as the fallback for any other
 * unsuccessful status code.
 */
class NetworkException extends YandexMusicException
{
}
