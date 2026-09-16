# PHP 8.3 is keg-only under Homebrew, so it is not on PATH by default. Prefer
# whatever `php` resolves to, fall back to the keg, and refuse to run on a
# version older than the library supports.
PHP ?= $(shell command -v php 2>/dev/null)
ifeq ($(PHP),)
PHP := /opt/homebrew/opt/php@8.3/bin/php
endif

COMPOSER ?= composer
MIN_VERSION := 80300

.PHONY: help check-php install test coverage stan cs cs-fix check

help:
	@echo 'install   install dependencies'
	@echo 'test      run the test suite'
	@echo 'coverage  run the test suite with a coverage report'
	@echo 'stan      run static analysis'
	@echo 'cs        check code style'
	@echo 'cs-fix    fix code style in place'
	@echo 'check     test + stan + cs, what CI would run'

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

check: test stan cs
