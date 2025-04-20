################################################################################
# Makefile for Curlmetry project
#
# Author: Sarigue
# Project: Curlmetry - PSR-18 / PSR-7 OpenTelemetry trace exporter for PHP 5.6
# License: Apache 2.0
# Description: Provides automation for code style checks, linting and unit testing
################################################################################

# === Configuration ===

PHP_CS=vendor/bin/phpcs           # PHP_CodeSniffer binary
PHP_BF=vendor/bin/phpcbf          # PHP_CodeSniffer autofix binary
PHPUNIT=vendor/bin/phpunit        # PHPUnit binary
PHPSTAN=vendor/bin/phpstan        # PHPStan binary

RULESET=ruleset.xml               # PHP_CodeSniffer custom ruleset
SRC=./src                         # Application source code
TESTS=./tests                     # PHPUnit test directory

# === Default target ===

all: lint coverage

# === Linting and Static Analysis ===

## Run PHP_CodeSniffer with custom ruleset
lint:
	@echo "Running PHP_CodeSniffer..."
	@$(PHP_CS) -s --standard=$(RULESET) $(SRC) $(TESTS)

## Run PHPStan static analyzer
lint-phpstan:
	@echo "Running PHPStan..."
	@$(PHPSTAN) analyse --memory-limit=512M

## Show enabled sniffs (debug)
rules:
	@echo "Listing active PHP_CodeSniffer sniffs..."
	@$(PHP_CS) -e --standard=$(RULESET)

# === Unit Testing ===

## Run PHPUnit test suite
test:
	@echo "Running PHPUnit tests..."
	@$(PHPUNIT) --colors=always $(TESTS)

## Generate code coverage reports (HTML + XML + text)
coverage:
	@echo "Generating PHPUnit coverage reports..."
	@$(PHPUNIT) \
		--coverage-html coverage-html \
		--coverage-clover coverage.xml \
		--coverage-text \
		$(TESTS)
	@echo "HTML report:       ./coverage-html/index.html"
	@echo "Clover XML report: ./coverage.xml"

## Run PHP_CodeSniffer fix with custom ruleset
autofix:
	@echo "Autofix code with PHP_CodeSniffer..."
	@$(PHP_BF) -s --standard=$(RULESET) $(SRC) $(TESTS)

# === Maintenance ===

## Display help
help:
	@echo "Available make targets:"
	@echo "  make             - Run code sniffer, PHPStan, and PHPUnit"
	@echo "  make lint        - Run PHP_CodeSniffer on src/ and tests/"
	@echo "  make lint-phpstan- Run PHPStan static analysis"
	@echo "  make rules       - List enabled PHP_CodeSniffer rules"
	@echo "  make test        - Run PHPUnit tests"
	@echo "  make coverage    - Generate HTML code coverage report"
	@echo "  make help        - Display this help message"

## Clean temporary or generated files (stub)
clean:
	@echo "Nothing to clean for now."