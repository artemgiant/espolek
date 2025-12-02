# ============================================================================
# COMPOSER ТА NPM
# ============================================================================

.PHONY: composer-install composer-update npm-install npm-dev npm-build npm-watch

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
