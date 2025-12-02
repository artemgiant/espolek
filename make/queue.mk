# ============================================================================
# ЧЕРГИ ТА HORIZON (ЛОКАЛЬНО)
# ============================================================================

.PHONY: queue horizon horizon-daemon horizon-status horizon-pause horizon-continue
.PHONY: horizon-terminate horizon-purge queue-work queue-listen queue-failed
.PHONY: queue-retry queue-flush queue-restart
.PHONY: redis-cli redis-monitor redis-info redis-flush redis-queue-size

# Horizon
horizon: ## Запустити Laravel Horizon
	@echo "${GREEN}Запуск Horizon...${RESET}"
	./vendor/bin/sail artisan horizon

horizon-daemon: ## Запустити Horizon в фоні
	@echo "${GREEN}Запуск Horizon в фоновому режимі...${RESET}"
	./vendor/bin/sail exec -d laravel.test php artisan horizon
	@echo "${GREEN}✓ Horizon запущено в фоні!${RESET}"

horizon-status: ## Показати статус Horizon
	./vendor/bin/sail artisan horizon:status

horizon-pause: ## Призупинити обробку черг Horizon
	@echo "${YELLOW}Призупинення Horizon...${RESET}"
	./vendor/bin/sail artisan horizon:pause
	@echo "${GREEN}✓ Horizon призупинено!${RESET}"

horizon-continue: ## Продовжити обробку черг Horizon
	@echo "${GREEN}Відновлення роботи Horizon...${RESET}"
	./vendor/bin/sail artisan horizon:continue
	@echo "${GREEN}✓ Horizon відновлено!${RESET}"

horizon-terminate: ## Зупинити Horizon (graceful shutdown)
	@echo "${YELLOW}Зупинка Horizon...${RESET}"
	./vendor/bin/sail artisan horizon:terminate
	@echo "${GREEN}✓ Horizon зупинено!${RESET}"

horizon-purge: ## Очистити всі метрики Horizon
	@echo "${YELLOW}⚠ Увага: Всі метрики Horizon будуть видалено!${RESET}"
	@read -p "Продовжити? (y/n): " confirm && [ $$confirm = y ] || exit 1
	./vendor/bin/sail artisan horizon:purge
	@echo "${GREEN}✓ Метрики очищено!${RESET}"

# Queue
queue: ## Запустити queue worker
	@read -p "Яку чергу запустити? (default: 'equipments,simcards,equipments-deactivate'): " QUEUE_NAME; \
	QUEUE_NAME=$${QUEUE_NAME:-equipments,equipments-deactivate,simcards}; \
	./vendor/bin/sail artisan queue:work --queue=$$QUEUE_NAME -vv

queue-work: ## Запустити обробник черг
	./vendor/bin/sail artisan queue:work --tries=3

queue-listen: ## Слухати черги (з перезавантаженням при змінах)
	./vendor/bin/sail artisan queue:listen

queue-failed: ## Показати проваленні завдання
	./vendor/bin/sail artisan queue:failed

queue-retry: ## Повторити проваленні завдання
	@read -p "Enter job ID (or 'all' for all failed jobs): " job_id; \
	./vendor/bin/sail artisan queue:retry $$job_id

queue-flush: ## Видалити всі проваленні завдання
	@echo "${YELLOW}⚠ Увага: Всі проваленні завдання будуть видалено!${RESET}"
	@read -p "Продовжити? (y/n): " confirm && [ $$confirm = y ] || exit 1
	./vendor/bin/sail artisan queue:flush

queue-restart: ## Перезапустити обробники черг
	@echo "${YELLOW}Перезапуск обробників черг...${RESET}"
	./vendor/bin/sail artisan queue:restart
	@echo "${GREEN}✓ Обробники перезапущено!${RESET}"

# Redis
redis-cli: ## Зайти в Redis CLI
	./vendor/bin/sail redis-cli

redis-monitor: ## Моніторинг Redis команд в реальному часі
	./vendor/bin/sail redis-cli monitor

redis-info: ## Показати інформацію про Redis
	./vendor/bin/sail redis-cli info

redis-flush: ## Очистити всі дані Redis
	@echo "${YELLOW}⚠ Увага: Всі дані Redis будуть видалено!${RESET}"
	@read -p "Продовжити? (y/n): " confirm && [ $$confirm = y ] || exit 1
	./vendor/bin/sail redis-cli flushall
	@echo "${GREEN}✓ Redis очищено!${RESET}"

redis-queue-size: ## Показати розмір черг в Redis
	@echo "${GREEN}Розмір черг:${RESET}"
	@./vendor/bin/sail redis-cli llen queues:default || echo "default: 0"
	@./vendor/bin/sail redis-cli llen queues:high || echo "high: 0"
	@./vendor/bin/sail redis-cli llen queues:low || echo "low: 0"
