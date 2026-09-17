<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Examples\Support;

use LuckyWins\YandexMusic\Examples\Support\Bootstrap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Where the examples look for a token.
 *
 * Nothing here reads the file — only where it is expected to be. That is the
 * part that broke once: moving this class one directory deeper moved the path
 * it computed with it, and every example then reported having no token while
 * the token sat where it always had. The failure looked like an expired
 * credential, which is the worst thing it could have looked like.
 */
#[CoversClass(Bootstrap::class)]
final class BootstrapTest extends TestCase
{
    public function testTheTokenIsLookedForInTheRepositoryRoot(): void
    {
        $directory = dirname(Bootstrap::envPath());

        self::assertFileExists(
            $directory.'/composer.json',
            'the token file belongs beside composer.json, not beside whatever directory this class was moved into',
        );
    }

    public function testItIsTheFileTheDocumentationNames(): void
    {
        self::assertSame('.env.local', basename(Bootstrap::envPath()));
    }

    /**
     * The committed twin has to sit beside it, or the instructions for putting
     * a token in place point at nothing.
     */
    public function testTheExampleFileSitsBesideIt(): void
    {
        self::assertFileExists(Bootstrap::envPath().'.example');
    }
}
