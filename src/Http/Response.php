<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Http;

/**
 * The envelope Yandex wraps every API response in.
 *
 * Successful responses carry the payload under `result`, alongside an
 * `invocationInfo` block that is only ever useful for support requests. Some
 * endpoints — the OAuth ones among them — answer with the payload at the root
 * instead, so an absent `result` key means the whole body is the result.
 *
 * Errors arrive in two shapes. The API uses an object, `{"error": {"name":
 * ..., "message": ...}}`, while OAuth uses a flat pair of strings, `{"error":
 * "authorization_pending", "error_description": ...}`. Both are normalized
 * here into a code and a description, so callers never have to care which
 * endpoint they are talking to.
 */
final class Response
{
    /**
     * @param mixed $data   the decoded body, exactly as it arrived
     * @param mixed $result the payload: the `result` key, or the whole body
     */
    private function __construct(
        public readonly mixed $data,
        public readonly mixed $result,
        public readonly ?string $errorCode,
        public readonly ?string $errorDescription,
    ) {
    }

    public static function fromDecoded(mixed $data): self
    {
        if (!is_array($data)) {
            return new self($data, $data, null, null);
        }

        $result = array_key_exists('result', $data) ? $data['result'] : $data;

        [$code, $description] = self::readError($data);

        return new self($data, $result, $code, $description);
    }

    public function hasError(): bool
    {
        return null !== $this->errorCode;
    }

    /**
     * The error as a single line, for an exception message.
     */
    public function errorMessage(): string
    {
        if (null === $this->errorCode) {
            return 'Unknown error';
        }

        if (null === $this->errorDescription || '' === $this->errorDescription) {
            return $this->errorCode;
        }

        return $this->errorCode.': '.$this->errorDescription;
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array{0: ?string, 1: ?string} the error code and its description
     */
    private static function readError(array $data): array
    {
        $error = $data['error'] ?? null;

        if (is_string($error) && '' !== $error) {
            // OAuth shape. Yandex sends error_description; the camelCase spelling
            // is accepted too because parts of the music API use it.
            $description = $data['error_description'] ?? $data['errorDescription'] ?? null;

            return [$error, is_string($description) ? $description : null];
        }

        if (is_array($error)) {
            $name = $error['name'] ?? null;
            $message = $error['message'] ?? null;

            return [
                is_string($name) ? $name : null,
                is_string($message) ? $message : null,
            ];
        }

        return [null, null];
    }
}
