<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Support;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Model;
use PHPUnit\Framework\TestCase;

/**
 * The five cases every model is required to cover, implemented once.
 *
 * Subclasses supply the model class and two payloads; the cases themselves —
 * empty payload, required fields only, all fields, and equality — are the same
 * for every model and live here. Anything a particular model needs beyond that
 * goes in the subclass as an extra test.
 */
abstract class ModelTestCase extends TestCase
{
    /**
     * An API payload with every field this model understands.
     *
     * @return array<string, mixed>
     */
    abstract protected static function fullPayload(): array;

    /**
     * The smallest payload that still deserializes.
     *
     * @return array<string, mixed>
     */
    abstract protected static function requiredPayload(): array;

    /**
     * Assert the model built from fullPayload() came out right. This is where
     * a model's own fields actually get checked.
     */
    abstract protected function assertFullyPopulated(Model $model): void;

    /**
     * Two models that must compare equal, and one that must not.
     *
     * @return array{0: Model, 1: Model, 2: Model} same, sameAgain, different
     */
    abstract protected function equalityTriple(): array;

    /** @return class-string<Model> */
    abstract protected static function modelClass(): string;

    public function testEmptyPayloadYieldsNull(): void
    {
        $class = static::modelClass();

        self::assertNull($class::fromApi([], self::client()));
        self::assertNull($class::fromApi(null, self::client()));
    }

    public function testRequiredFieldsAreEnough(): void
    {
        $class = static::modelClass();

        self::assertInstanceOf($class, $class::fromApi(static::requiredPayload(), self::client()));
    }

    public function testEveryFieldIsRead(): void
    {
        $class = static::modelClass();
        $model = $class::fromApi(static::fullPayload(), self::client());

        self::assertInstanceOf($class, $model);
        $this->assertFullyPopulated($model);
    }

    /**
     * A payload missing a required field is a changed API, and has to say so
     * rather than fail somewhere later with a type error.
     */
    public function testMissingRequiredFieldIsReported(): void
    {
        $required = static::requiredPayload();

        if ([] === $required) {
            self::markTestSkipped('This model has no required fields.');
        }

        $short = $required;
        unset($short[array_key_first($short)]);

        if ([] === $short) {
            // Dropping the only required field leaves an empty payload, which
            // means null by design rather than an error.
            self::assertNull(static::modelClass()::fromApi($short, self::client()));

            return;
        }

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('missing required fields');

        static::modelClass()::fromApi($short, self::client());
    }

    public function testEquality(): void
    {
        [$a, $b, $c] = $this->equalityTriple();

        self::assertTrue($a->equals($b), 'models with the same identity must compare equal');
        self::assertFalse($a->equals($c), 'models with different identities must not');
        self::assertFalse($a->equals(null));
    }

    protected static function client(): Client
    {
        return new Client(request: new Request(new MockHttpClient()));
    }

    /**
     * A client that reports the fields its models could not place.
     */
    protected static function clientReportingUnknownFields(RecordingLogger $logger): Client
    {
        return new Client(
            request: new Request(new MockHttpClient()),
            reportUnknownFields: true,
            logger: $logger,
        );
    }
}
