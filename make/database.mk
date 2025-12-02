# ============================================================================
# БАЗА ДАНИХ
# ============================================================================

.PHONY: migrate migrate-fresh migrate-seed seed rollback

migrate: ## Запустити міграції
	@echo "${GREEN}Запуск міграцій...${RESET}"
	./vendor/bin/sail artisan migrate
	@echo "${GREEN}✓ Міграції виконано!${RESET}"

migrate-fresh: ## Скинути БД та запустити міграції заново
	@echo "${YELLOW}⚠ Увага: Всі дані будуть видалено!${RESET}"
	@read -p "Продовжити? (y/n): " confirm && [ $$confirm = y ] || exit 1
	./vendor/bin/sail artisan migrate:fresh
	@echo "${GREEN}✓ База даних оновлена!${RESET}"

migrate-seed: ## Запустити міграції та seeders
	@echo "${GREEN}Запуск міграцій та seeders...${RESET}"
	./vendor/bin/sail artisan migrate --seed
	@echo "${GREEN}✓ Готово!${RESET}"

seed: ## Запустити seeders
	@read -p "Enter seeder class (or press Enter for all): " class; \
	if [ -z "$$class" ]; then \
		./vendor/bin/sail artisan db:seed; \
	else \
		./vendor/bin/sail artisan db:seed --class=$$class; \
	fi

rollback: ## Відкотити останню міграцію
	@read -p "Enter number of migrations to rollback (or press Enter to cancel): " steps; \
	if [ -z "$$steps" ]; then \
		echo "Rollback cancelled"; \
	else \
		./vendor/bin/sail artisan migrate:rollback --step=$$steps; \
	fi
