# ============================================================================
# УТИЛІТИ
# ============================================================================

.PHONY: install clean optimize fresh tinker permissions fix-permissions

install: ## Установка залежностей та початкове налаштування
	@echo "${GREEN}Установка проекту...${RESET}"
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
	@if [ ! -f .env ]; then cp .env.example .env; fi
	chmod +x ./vendor/bin/sail
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan key:generate
	@echo "${GREEN}✓ Проект встановлено!${RESET}"

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

tinker: ## Запустити tinker
	./vendor/bin/sail artisan tinker

permissions: ## Виправити права доступу
	@echo "${GREEN}Виправлення прав доступу...${RESET}"
	sudo chown -R $$USER:$$USER .
	chmod +x ./vendor/bin/sail
	@echo "${GREEN}✓ Права виправлено!${RESET}"

fix-permissions: ## Виправити права доступу (альтернатива)
	sudo chown -R $(USER):$(USER) .
