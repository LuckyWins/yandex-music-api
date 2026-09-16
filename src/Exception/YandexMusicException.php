<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Exception;

use Exception;
use Throwable;

/**
 * Base class for every exception the library raises.
 *
 * Catching this catches everything, including malformed or undecodable server
 * responses, which are raised as this class directly.
 *
 * Where the server named a machine-readable error code, it is carried on the
 * exception rather than only interpolated into the message, so callers can
 * branch on the code instead of matching against message text.
 */
class YandexMusicException extends Exception
{
    public function __construct(
        string $message = '',
        private readonly ?string $errorCode = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * The error code the server sent, such as `authorization_pending`, or null
     * when the failure did not come with one.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}
