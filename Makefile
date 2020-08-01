#!/bin/bash

all: phan phpstan psalm

.PHONY: phan
phan:
	@echo "=== Phan ==="
	@php -n \
		-d extension=ast \
		-d extension=json \
		-d extension=phar \
		-d extension=tokenizer \
		vendor/bin/phan.phar -p --color ||:

.PHONY: phpstan
phpstan:
	@echo "=== PHPStan ==="
	@php -n \
		-d extension=json \
		-d extension=mbstring \
		-d extension=phar \
		-d extension=tokenizer \
		vendor/bin/phpstan.phar analyse ||:

.PHONY: psalm
psalm:
	@echo "=== Psalm ==="
	@php -n \
		-d extension=dom \
		-d extension=json \
		-d extension=phar \
		-d extension=simplexml \
		-d extension=tokenizer \
		vendor/bin/psalm ||:
