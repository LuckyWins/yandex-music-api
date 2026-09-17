<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use LuckyWins\YandexMusic\Tests\Support\RecordingLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * What a model says when the API sends it something it cannot place.
 *
 * This is the whole drift detector: nothing else notices Yandex adding a field.
 * So it has to name the field, say what shape arrived in it — and never repeat
 * the value, because these responses are about somebody's account.
 */
#[CoversClass(Model::class)]
#[CoversClass(Client::class)]
final class UnknownFieldReportingTest extends TestCase
{
    /**
     * Pager has three fields, which makes everything else in a payload
     * unplaceable — convenient for a test about being unplaceable.
     */
    private const PAYLOAD = [
        'total' => 100,
        'page' => 0,
        'perPage' => 20,
        'label' => 'первая страница',
        'ratio' => 0.5,
        'migrated' => true,
        'retiredAt' => null,
        'owner' => ['login' => 'someone'],
        'sections' => [['id' => 1], ['id' => 2]],
        'kinds' => ['rock', 'pop'],
        'nothingYet' => [],
        'matrix' => [[1, 2], [3, 4]],
    ];

    public function testTheFieldsAreReportedByName(): void
    {
        $logger = new RecordingLogger();

        Pager::fromApi(self::PAYLOAD, self::reportingClient($logger));

        self::assertSame(
            ['label', 'ratio', 'migrated', 'retiredAt', 'owner', 'sections', 'kinds', 'nothingYet', 'matrix'],
            $logger->unknownFields(),
            'fields stays a plain list of names, which is what anything already reading these logs expects',
        );
    }

    /**
     * The shape decides the fix: a scalar is a property, an object is a model,
     * and a list of objects is a NESTED list. A report that says `array` to all
     * three saves nobody any work.
     */
    /**
     * An empty value is reported as `empty` rather than guessed at: json_decode
     * turns `[]` and `{}` into the same thing, and a wrong guess here sends
     * somebody off to write the wrong kind of model.
     */
    public function testWhatArrivedInEachFieldIsNamedByShape(): void
    {
        $logger = new RecordingLogger();

        Pager::fromApi(self::PAYLOAD, self::reportingClient($logger));

        self::assertSame([
            'label' => 'string',
            'ratio' => 'float',
            'migrated' => 'bool',
            'retiredAt' => 'null',
            'owner' => 'object',
            'sections' => 'list<object>',
            'kinds' => 'list<string>',
            'nothingYet' => 'empty',
            'matrix' => 'list<list>',
        ], $logger->unknownTypes());
    }

    public function testTheModelThatCouldNotPlaceThemIsNamed(): void
    {
        $logger = new RecordingLogger();

        Pager::fromApi(self::PAYLOAD, self::reportingClient($logger));

        self::assertSame([Pager::class], $logger->models());
    }

    /**
     * The one thing a report must never carry.
     *
     * An unplaced field is as likely to hold a phone number as a feature flag,
     * and these reports end up in logs and in issues. Names and shapes only.
     */
    public function testTheValuesThemselvesNeverReachTheLogger(): void
    {
        $logger = new class () extends AbstractLogger {
            /** @var array<string, mixed> */
            private array $captured = [];

            /**
             * @param array<string, mixed> $context
             */
            public function log(mixed $level, string|Stringable $message, array $context = []): void
            {
                $this->captured = $context;
            }

            /** @return array<string, mixed> */
            public function captured(): array
            {
                return $this->captured;
            }
        };

        Pager::fromApi([
            'total' => 1,
            'page' => 0,
            'perPage' => 1,
            'phone' => '+70000000000',
            'owner' => ['login' => 'someone'],
        ], self::reportingClient($logger));

        $reported = json_encode($logger->captured(), JSON_THROW_ON_ERROR);

        self::assertStringContainsString('phone', $reported);
        self::assertStringNotContainsString('+70000000000', $reported);
        self::assertStringNotContainsString('someone', $reported);
    }

    public function testNothingIsSaidWhenEveryFieldHasAHome(): void
    {
        $logger = new RecordingLogger();

        Pager::fromApi(['total' => 100, 'page' => 0, 'perPage' => 20], self::reportingClient($logger));

        self::assertSame([], $logger->unknownFields());
        self::assertSame([], $logger->unknownTypes());
    }

    /**
     * Reporting is opt-in, and staying quiet is the default.
     */
    public function testNothingIsSaidWhenReportingIsOff(): void
    {
        $logger = new RecordingLogger();
        $client = new Client(request: new Request(new MockHttpClient()), logger: $logger);

        Pager::fromApi(self::PAYLOAD, $client);

        self::assertSame([], $logger->unknownFields());
    }

    private static function reportingClient(\Psr\Log\LoggerInterface $logger): Client
    {
        return new Client(
            request: new Request(new MockHttpClient()),
            reportUnknownFields: true,
            logger: $logger,
        );
    }
}
