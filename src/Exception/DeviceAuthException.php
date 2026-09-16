<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Exception;

/**
 * The OAuth device flow failed.
 *
 * Raised when the user declines, the device code expires, the caller cancels,
 * or the wait for confirmation times out. A poll that is merely still waiting
 * is not an error and does not raise this.
 */
class DeviceAuthException extends YandexMusicException
{
}
