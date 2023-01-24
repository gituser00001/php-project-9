PORT ?= 8000
start:
	php -S 0.0.0.0:$(PORT) -t public

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public