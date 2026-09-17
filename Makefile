# PHP 8.3 is keg-only under Homebrew, so it is not on PATH by default. Prefer
# whatever `php` resolves to, fall back to the keg, and refuse to run on a
# version older than the library supports.
PHP ?= $(shell command -v php 2>/dev/null)
ifeq ($(PHP),)
PHP := /opt/homebrew/opt/php@8.3/bin/php
endif

COMPOSER ?= composer
MIN_VERSION := 80300

.PHONY: help check-php install test coverage stan cs cs-fix docs docs-check check audit

help:
	@echo 'install   install dependencies'
	@echo 'test      run the test suite'
	@echo 'coverage  run the test suite with a coverage report'
	@echo 'stan      run static analysis'
	@echo 'cs        check code style'
	@echo 'cs-fix    fix code style in place'
	@echo 'docs      regenerate the model and endpoint reference'
	@echo 'check     test + stan + cs + docs, what CI would run'
	@echo 'audit     find fields the models are missing, live — needs a token'

check-php:
	@command -v $(PHP) >/dev/null 2>&1 || { \
		echo 'php not found. Install it with: brew install php@8.3'; \
		echo 'Then either add /opt/homebrew/opt/php@8.3/bin to PATH, or run: make PHP=/opt/homebrew/opt/php@8.3/bin/php'; \
		exit 1; \
	}
	@$(PHP) -r 'exit(PHP_VERSION_ID >= $(MIN_VERSION) ? 0 : 1);' || { \
		echo "This library needs PHP 8.3 or newer; $(PHP) is $$($(PHP) -r 'echo PHP_VERSION;')"; \
		exit 1; \
	}

install: check-php
	$(COMPOSER) install

test: check-php
	$(PHP) vendor/bin/phpunit

coverage: check-php
	$(PHP) -d xdebug.mode=coverage vendor/bin/phpunit --coverage-text

stan: check-php
	$(PHP) vendor/bin/phpstan analyse

cs: check-php
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: check-php
	$(PHP) vendor/bin/php-cs-fixer fix

docs: check-php
	$(PHP) tools/generate-docs.php

# Fails when the committed reference no longer matches the code. Documentation
# that can go stale unnoticed is worse than none.
docs-check: docs
	@# Refresh the index first. Regenerating the docs gives them new timestamps,
	@# and files whose timestamp matches the index's are "racily clean" to git:
	@# it reports them as changed until it has re-read them, which made this
	@# check fail spuriously on the first run after a commit.
	@git update-index -q --refresh || true
	@git diff --exit-code -- docs/models.md docs/endpoints.md \
		|| { echo 'docs/ is out of date — run `make docs` and commit the result'; exit 1; }

check: test stan cs docs-check

# What `check` cannot answer: whether the models still match what the service
# sends. Calls every reading endpoint and reports the fields no model declares.
#
# The Python reference is not consulted. It was the map while the library was
# being ported and the port is done; from here the service itself is the only
# thing worth checking against, and where the two disagree the service wins.
# `tools/compare-with-reference.php` is still there for a one-off comparison.
#
# Deliberately not part of `check`: it needs a token and a network, and CI has
# neither. It ends quietly rather than failing when there is no token.
audit: check-php
	@$(PHP) examples/audit.php
