PORT ?= 8000
start:
	php -S 0.0.0.0:$(PORT) -t public

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public src

localstart:
	php -S localhost:8080 -t public public/index.php