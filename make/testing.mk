# ============================================================================
# ТЕСТУВАННЯ
# ============================================================================

.PHONY: test test-coverage pint

test: ## Запустити тести
	./vendor/bin/sail artisan test

test-coverage: ## Запустити тести з покриттям
	./vendor/bin/sail artisan test --coverage

pint: ## Виправити код style (Laravel Pint)
	./vendor/bin/sail pint
