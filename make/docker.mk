# ============================================================================
# DOCKER КОМАНДИ
# ============================================================================

.PHONY: up down restart build shell root-shell mysql mysql-root ps stats prune

up: ## Запустити Docker контейнери
	@echo "${GREEN}Запуск контейнерів...${RESET}"
	./vendor/bin/sail up -d
	@echo "${GREEN}Запуск Horizon...${RESET}"
	./vendor/bin/sail exec -d laravel.test php artisan horizon
	@echo "${GREEN}✓ Контейнери запущено!${RESET}"
	@echo "${YELLOW}Сайт: http://localhost:8000${RESET}"
	@echo "${YELLOW}Horizon: http://localhost:8000/horizon${RESET}"
	@echo "${YELLOW}phpMyAdmin: http://localhost:8081${RESET}"

down: ## Зупинити Docker контейнери
	@echo "${YELLOW}Зупинка контейнерів...${RESET}"
	./vendor/bin/sail down
	@echo "${GREEN}✓ Контейнери зупинено!${RESET}"

restart: ## Перезапустити контейнери
	@echo "${YELLOW}Перезапуск контейнерів...${RESET}"
	./vendor/bin/sail restart
	@echo "${GREEN}✓ Контейнери перезапущено!${RESET}"

build: ## Перебудувати Docker образи
	@echo "${GREEN}Перебудова образів...${RESET}"
	./vendor/bin/sail build --no-cache
	@echo "${GREEN}✓ Образи перебудовано!${RESET}"

shell: ## Зайти в контейнер Laravel (bash)
	./vendor/bin/sail shell

root-shell: ## Зайти в контейнер Laravel як root
	./vendor/bin/sail root-shell

mysql: ## Зайти в MySQL консоль
	./vendor/bin/sail mysql

mysql-root: ## Зайти в MySQL як root
	docker exec -it $$(docker ps --filter name=mysql --format "{{.Names}}") mysql -u root -ppassword

ps: ## Показати запущені контейнери
	docker ps --filter name=$$(basename $$(pwd))

stats: ## Показати статистику контейнерів
	docker stats --no-stream

prune: ## Видалити невикористані Docker ресурси
	@echo "${YELLOW}⚠ Видалення невикористаних ресурсів...${RESET}"
	docker system prune -f
	@echo "${GREEN}✓ Готово!${RESET}"
