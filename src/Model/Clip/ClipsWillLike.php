<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Clip;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;

/**
 * A page of clips: the ones liked, or the ones suggested.
 */
final class ClipsWillLike extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'clips' => [Clip::class, 'list'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        /** @var list<Clip> */
        public readonly array $clips = [],
        public readonly ?Pager $pager = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->clips, $this->pager];
    }
}
