<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * The port is finished, and this is what says so.
 *
 * Nine stages moved one domain at a time out of a trait called Legacy, whose
 * methods handed back whatever json_decode produced. That trait is gone, and
 * these keep it gone: a method that returns `mixed`, or a trait by that name
 * coming back, would mean the shape of a response is a caller's problem again.
 */
#[CoversClass(Client::class)]
final class PortedSurfaceTest extends TestCase
{
    public function testThereIsNoLegacyTrait(): void
    {
        self::assertNotContains(
            'LuckyWins\YandexMusic\Client\Legacy',
            (new ReflectionClass(Client::class))->getTraitNames(),
        );
        self::assertFalse(
            trait_exists('LuckyWins\YandexMusic\Client\Legacy'),
            'the trait itself should be gone, not merely unused',
        );
    }

    /**
     * Every public method says what it returns, and none of them says `mixed`.
     */
    public function testNoMethodReturnsRawData(): void
    {
        $untyped = [];

        foreach ((new ReflectionClass(Client::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()) {
                continue;
            }

            $type = $method->getReturnType();

            if (!$type instanceof ReflectionNamedType || 'mixed' === $type->getName()) {
                $untyped[] = $method->getName();
            }
        }

        self::assertSame([], $untyped);
    }
}
