# TODO — ideas for later

Everything deliberately postponed. The current work plan is not duplicated
here; this is only what to come back to.

---

## Port

- [ ] **Give `TrackFullInfo::$otherVersions` a model.** It is one of the few
      fields still declared as a raw array, because nothing has ever arrived in
      it: sixty-four tracks across the chart and four searches all sent it
      empty, and an empty array says nothing about whether it holds a list or a
      map. The reference library does not have the field at all.

      It needs a track that actually has other cuts — a song with a well known
      remix or live version. Once one is found, the shape follows from one
      response and the field becomes a NESTED list like the rest.

- [ ] **Retries on 429 and 5xx.** `Http\Request` sends once and turns anything
      unsuccessful into an exception. A rate-limited or briefly unavailable
      response is not the same kind of failure as a 404, and a caller sweeping
      a hundred endpoints has to write the backoff themselves.

      Not urgent: nothing has hit a rate limit yet. Worth doing before anything
      here runs unattended.

- [ ] **Creating a playback queue.** `queueCreate()` is written and refused:
      `POST /queues` answers `400 Can't parse body` to JSON and closes the
      connection on anything else. Five body shapes were tried, including the
      one the reference library sends, which fails the same way. Reading
      queues works. The likely answer is that current clients speak protobuf
      here — which is also what Ynison below uses, so the two may be one piece
      of work.

      What was tried, so the next attempt need not repeat it: JSON with null
      fields, JSON without them, JSON without `from`, the raw JSON string with
      a form content type, and that string as a form field. The JSON ones
      answer `400 Can't parse body`; the others close the connection with no
      reply, which is how this API reacts to a body of the wrong shape — the
      same way `/pin/wave` reacts to seeds sent as a string.

      Worth trying next: capturing what the Android app actually sends, and
      `yandex_music/ynison` in the reference, which already speaks the
      protocol.

- [ ] **Ynison.** The reference gained a websocket protocol for remote player
      control and cross-device state. Nothing here touches it.
