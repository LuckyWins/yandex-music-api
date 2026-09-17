<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Skeleton;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where "see all" leads, in an app and on the web.
 */
final class SkeletonViewAllAction extends Model
{
    public function __construct(
        public readonly ?string $deeplink = null,
        public readonly ?string $weblink = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->deeplink, $this->weblink];
    }
}
