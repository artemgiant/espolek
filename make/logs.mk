# ============================================================================
# ЛОГИ ТА МОНІТОРИНГ
# ============================================================================

.PHONY: logs logs-app logs-mysql logs-redis logs-horizon logs-queue logs-follow

logs: ## Показати логи всіх контейнерів
	./vendor/bin/sail logs

logs-app: ## Показати логи Laravel
	./vendor/bin/sail logs laravel.test

logs-mysql: ## Показати логи MySQL
	./vendor/bin/sail logs mysql

logs-redis: ## Показати логи Redis
	./vendor/bin/sail logs redis

logs-horizon: ## Показати логи Horizon (з Laravel logs)
	tail -f storage/logs/horizon.log 2>/dev/null || tail -f storage/logs/laravel.log | grep -i horizon

logs-queue: ## Показати логи черг (з Laravel logs)
	tail -f storage/logs/laravel.log | grep -i queue

logs-follow: ## Слідкувати за логами в реальному часі
	./vendor/bin/sail logs -f
