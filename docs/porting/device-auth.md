# Device auth

Ported in the first stage, alongside the move to PHP 8.3.

## Why it had to change

The library authenticated with the OAuth password grant. Yandex withdrew it,
and the reference library removed it outright — only the device flow remains.
Everything else in that stage existed to give the replacement somewhere to live.

## Methods

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| `fromCredentials()` | **removed** | removed there too | the grant it used no longer exists |
| — | `requestDeviceCode()` | `request_device_code()` | none |
| — | `pollDeviceToken()` | `poll_device_token()` | pending is detected by comparing the error code, not by searching the message text for a substring |
| — | `deviceAuth()` | `device_auth()` | `slow_down` widens the interval per RFC 8628 instead of aborting |
| — | `revokeToken()` | **absent** | ours; there is no other way to kill a leaked token |
| — | `forgetToken()` | **absent** | ours; drops the credential without rebuilding the client |

## Worth knowing

The OAuth credentials are the same ones the 2019 code used and the same ones
the reference uses. Whose application they belong to is not verifiable — the
reference describes them as an official Yandex.Music client, and Yandex does not
let anyone register their own.

Revocation is the part most easily got wrong. `POST /revoke_token` answers
`{"status": "ok"}` for a token that never existed, so the call proves nothing;
only a subsequent 401 does. Ending the session in Yandex ID under "Devices and
sessions" does not invalidate a token at all — that list is about sign-ins.
