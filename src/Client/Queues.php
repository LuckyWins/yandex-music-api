<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Queue\Queue;
use LuckyWins\YandexMusic\Model\Queue\QueueItem;

/**
 * Queues — what each device is playing, so another can pick it up.
 *
 * Every method here names a device. The one this client describes itself as
 * is used unless another is given; see Client::getDevice().
 */
trait Queues
{
    /**
     * The queues this account has, newest first.
     *
     * @return list<QueueItem>
     */
    public function queuesList(?string $device = null): array
    {
        $result = $this->request->get(
            $this->getBaseUrl().'/queues',
            headers: $this->deviceHeader($device),
        );

        return QueueItem::listFromApi(is_array($result) ? ($result['queues'] ?? null) : null, $this);
    }

    /**
     * One queue, with its tracks.
     */
    public function queue(string $queueId): ?Queue
    {
        return Queue::fromApi($this->request->get($this->getBaseUrl().'/queues/'.$queueId), $this);
    }

    /**
     * Say which track of a queue is playing now.
     */
    public function queueUpdatePosition(string $queueId, int $currentIndex, ?string $device = null): bool
    {
        $url = $this->getBaseUrl().'/queues/'.$queueId.'/update-position?'
            .http_build_query(['currentIndex' => $currentIndex]);

        $result = $this->request->post(
            $url,
            ['isInteractive' => 'False'],
            $this->deviceHeader($device),
        );

        // This one reports itself in a field rather than by answering `ok`.
        return is_array($result) && 'ok' === ($result['status'] ?? null);
    }

    /**
     * Hand a queue to the service, and get back the id it filed it under.
     *
     * **This does not currently work, and the reference library's version
     * does not either.** The endpoint answers `400 Can't parse body` to a JSON
     * body, and closes the connection without a reply to a form — checked
     * against the live API with five shapes, including the raw JSON string the
     * reference sends. Reading queues works; creating one evidently wants
     * something this library has not found yet, most likely the protobuf the
     * current apps speak.
     *
     * It is kept because the shape is right as far as it goes, and because
     * having it here is how the next attempt starts.
     */
    public function queueCreate(Queue $queue, ?string $device = null): ?string
    {
        $result = $this->request->postJson(
            $this->getBaseUrl().'/queues',
            $queue->toArray(),
            $this->deviceHeader($device),
        );

        $id = is_array($result) ? ($result['id'] ?? null) : null;

        return is_string($id) ? $id : null;
    }

    /**
     * The device descriptor, sent with this request alone.
     *
     * @return array<string, string>
     */
    private function deviceHeader(?string $device): array
    {
        return ['X-Yandex-Music-Device' => $device ?? $this->getDevice()];
    }
}
