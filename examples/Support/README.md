# Support for the examples

Not examples. These three classes are what the scripts one directory up are
built from, kept apart so that `examples/` is exactly the list of things you
can run.

- `Bootstrap.php` — finds the token in `.env.local`, builds a client from it,
  and reads the script's arguments
- `UnknownFieldCollector.php` — a PSR-3 logger that gathers every field the API
  sent that no model declares, for a summary at the end of a run
- `Sweep.php` — keeps score of which endpoints `audit.php` reached, and names
  the ones it never did

They are loaded through `autoload-dev`, so nothing here ships to anyone who
installs the library.
