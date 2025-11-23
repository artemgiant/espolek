.PHONY: help install up down restart build shell root-shell mysql logs clean fresh test

# Кольори для виводу
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)
# Цвети для виводу
BLUE := \033[0;34m
GREEN := \033[0;32m
RED := \033[0;31m
NC := \033[0m # No Color
# Допомога
help: ## Показати всі доступні команди
	@echo ''
	@echo '${GREEN}Використання:${RESET}'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<команда>${RESET}'
	@echo ''
	@echo '${GREEN}Команди:${RESET}'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-20s${GREEN}%s${RESET}\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ''

fix-permissions:
	sudo chown -R $(USER):$(USER) .
#sudo chown -R $USER:$USER .

# Установка проекту
install: ## Установка залежностей та початкове налаштування
	@echo "${GREEN}Установка проекту...${RESET}"
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
	@if [ ! -f .env ]; then cp .env.example .env; fi
	chmod +x ./vendor/bin/sail
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan key:generate
	@echo "${GREEN}✓ Проект встановлено!${RESET}"

# Запуск та зупинка
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

# Робота з контейнерами
shell: ## Зайти в контейнер Laravel (bash)
	./vendor/bin/sail shell

root-shell: ## Зайти в контейнер Laravel як root
	./vendor/bin/sail root-shell

mysql: ## Зайти в MySQL консоль
	./vendor/bin/sail mysql

mysql-root: ## Зайти в MySQL як root
	docker exec -it $$(docker ps --filter name=mysql --format "{{.Names}}") mysql -u root -ppassword

# База даних
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

# Composer та NPM
composer-install: ## Встановити composer залежності
	./vendor/bin/sail composer install

composer-update: ## Оновити composer залежності
	./vendor/bin/sail composer update

npm-install: ## Встановити npm залежності
	./vendor/bin/sail npm install

npm-dev: ## Запустити npm dev (Vite)
	./vendor/bin/sail npm run dev

npm-build: ## Зібрати assets для продакшну
	./vendor/bin/sail npm run build

npm-watch: ## Запустити npm watch
	./vendor/bin/sail npm run watch

# Очищення
clean: ## Очистити кеш та логи
	@echo "${GREEN}Очищення кешу...${RESET}"
	./vendor/bin/sail artisan cache:clear
	./vendor/bin/sail artisan config:clear
	./vendor/bin/sail artisan route:clear
	./vendor/bin/sail artisan view:clear
	@echo "${GREEN}✓ Кеш очищено!${RESET}"

optimize: ## Оптимізувати Laravel (кешування)
	@echo "${GREEN}Оптимізація...${RESET}"
	./vendor/bin/sail artisan config:cache
	./vendor/bin/sail artisan route:cache
	./vendor/bin/sail artisan view:cache
	@echo "${GREEN}✓ Оптимізовано!${RESET}"

fresh: ## Повне оновлення проекту
	@echo "${GREEN}Повне оновлення проекту...${RESET}"
	./vendor/bin/sail down -v
	./vendor/bin/sail up -d
	./vendor/bin/sail composer install
	./vendor/bin/sail artisan key:generate
	./vendor/bin/sail artisan migrate:fresh --seed
	./vendor/bin/sail npm install
	./vendor/bin/sail npm run build
	@echo "${GREEN}✓ Проект оновлено!${RESET}"

# Artisan команди
tinker: ## Запустити tinker
	./vendor/bin/sail artisan tinker

queue: ## Запустити queue worker
	@read -p "Яку чергу запустити? (default: 'equipments,simcards,equipments-deactivate'): " QUEUE_NAME; \
	QUEUE_NAME=$${QUEUE_NAME:-equipments,equipments-deactivate,simcards}; \
	./vendor/bin/sail artisan queue:work --queue=$$QUEUE_NAME -vv

# Очереди та Horizon
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

# Тестування
test: ## Запустити тести
	./vendor/bin/sail artisan test

test-coverage: ## Запустити тести з покриттям
	./vendor/bin/sail artisan test --coverage

pint: ## Виправити код style (Laravel Pint)
	./vendor/bin/sail pint

# Логи та моніторинг
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

# Права доступу
permissions: ## Виправити права доступу
	@echo "${GREEN}Виправлення прав доступу...${RESET}"
	sudo chown -R $$USER:$$USER .
	chmod +x ./vendor/bin/sail
	@echo "${GREEN}✓ Права виправлено!${RESET}"

# Інше
ps: ## Показати запущені контейнери
	docker ps --filter name=$$(basename $$(pwd))

stats: ## Показати статистику контейнерів
	docker stats --no-stream

prune: ## Видалити невикористані Docker ресурси
	@echo "${YELLOW}⚠ Видалення невикористаних ресурсів...${RESET}"
	docker system prune -f
	@echo "${GREEN}✓ Готово!${RESET}"

# Змінні для підключення до сервера
SERVER_USER :=
SERVER_HOST :=
SERVER_PATH :=
SSH_KEY :=
DEPLOY_SCRIPT :=
DB_FLUSH_SCRIPT :=

SERVER_HOST_PROD :=
SERVER_PATH_PROD :=
BASH_SCRIPT_PROD :=


deploy:
	@echo "Запуск деплою на сервері..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'cd $(SERVER_PATH) && bash $(DEPLOY_SCRIPT)'
	@echo "Деплой завершено!"

# Скопіювати локальний deploy.sh на сервер і запустити
cleanup-db:
	@echo "Копіювання DB скрипту на сервер..."
	@scp -i $(SSH_KEY) $(DB_FLUSH_SCRIPT) $(SERVER_USER)@$(SERVER_HOST):$(SERVER_PATH)/
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'chmod +x $(SERVER_PATH)/$(DB_FLUSH_SCRIPT)'
	@echo "Запуск очищення БД..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'cd $(SERVER_PATH) && ./$(DB_FLUSH_SCRIPT)'
	@echo "БД очищена!"


run-bash-script-prod:
	@echo "Копіювання DB скрипту на сервер..."
	@scp -i $(SSH_KEY) $(BASH_SCRIPT_PROD) $(SERVER_USER)@$(SERVER_HOST_PROD):$(SERVER_PATH_PROD)/
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST_PROD) \
		'chmod +x $(SERVER_PATH_PROD)/$(BASH_SCRIPT_PROD)'
	@echo "Запуск очищення БД..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST_PROD) \
		'cd $(SERVER_PATH_PROD) && ./$(BASH_SCRIPT_PROD)'
	@echo "БД очищена!"


# Скопіювати локальний deploy.sh на сервер і запустити
deploy-local:
	@echo "Копіювання deploy.sh на сервер..."
	@scp -i $(SSH_KEY) $(DEPLOY_SCRIPT) $(SERVER_USER)@$(SERVER_HOST):$(SERVER_PATH)/
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'chmod +x $(SERVER_PATH)/$(DEPLOY_SCRIPT)'
	@echo "Запуск деплою..."
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'cd $(SERVER_PATH) && ./$(DEPLOY_SCRIPT)'
	@echo "Деплой завершено!"

# Перезапустити PHP-FPM
restart-php:
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'sudo systemctl restart php8.3-fpm'
	@echo "PHP-FPM перезапущено"

# Перезапустити Nginx
restart-nginx:
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'sudo systemctl restart nginx'
	@echo "Nginx перезапущено"


# Налаштування: перечитати + оновити + запустити
horizon-setup:
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"
	@echo "$(BLUE)        НАЛАШТУВАННЯ HORIZON$(NC)"
	@echo "$(BLUE)        НАЛАШТУВАННЯ HORIZON$(NC)"
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"
	@echo ""
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '$(YELLOW)1. Перечитування конфіг...$(NC)' && \
		sudo supervisorctl reread && \
		echo '' && \
		echo '$(YELLOW)2. Оновлення конфіг...$(NC)' && \
		sudo supervisorctl update && \
		echo '' && \
		echo '$(YELLOW)3. Запуск Horizon...$(NC)' && \
		sudo supervisorctl start horizon && \
		echo '' && \
		echo '$(GREEN)✓ Налаштування завершено!$(NC)'"
	@echo ""

# Перезавантаження Supervisor
supervisor-restart:
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '$(BLUE)► Перезавантаження Supervisor...$(NC)' && \
		sudo service supervisor restart && \
		echo '$(GREEN)✓ Supervisor перезавантажений$(NC)'"
	@echo ""

# Статус усіх Supervisor процесів
supervisor-status:
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	@echo "$(BLUE)► Статус усіх Supervisor процесів:$(NC)"
	@echo ""
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "sudo supervisorctl status"
	@echo ""

# Показати помилки (50 рядків)
horizon-logs-err:
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	@echo "$(BLUE)► Помилки Horizon (останні 50 рядків):$(NC)"
	@echo ""
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "tail -50 /var/log/supervisor/horizon-err.log"
	@echo ""

# Очистка логів
horizon-logs-clear:
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '$(BLUE)► Очистка логів...$(NC)' && \
		sudo truncate -s 0 /var/log/supervisor/horizon-out.log && \
		sudo truncate -s 0 /var/log/supervisor/horizon-err.log && \
		echo '$(GREEN)✓ Логи очищені$(NC)'"
	@echo ""

# Діагностика
horizon-diagnose:
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"
	@echo "$(BLUE)        ДІАГНОСТИКА HORIZON$(NC)"
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"
	@echo ""
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '' && \
		echo '$(YELLOW)1. Статус Horizon:$(NC)' && \
		sudo supervisorctl status horizon && \
		echo '' && \
		echo '$(YELLOW)2. Статус Supervisor:$(NC)' && \
		sudo supervisorctl status && \
		echo '' && \
		echo '$(YELLOW)3. Redis перевірка:$(NC)' && \
		redis-cli ping && \
		echo '' && \
		echo '$(YELLOW)4. Останні логи (50 рядків):$(NC)' && \
		tail -50 /var/log/supervisor/horizon-out.log && \
		echo '' && \
		echo '$(YELLOW)5. Помилки:$(NC)' && \
		tail -20 /var/log/supervisor/horizon-err.log"
	@echo ""
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"


horizon-restart:
	 sudo supervisorctl reread
	 sudo supervisorctl update
	 sudo supervisorctl restart horizon
	 sudo supervisorctl status
# За замовчуванням показати допомогу
.DEFAULT_GOAL := help
