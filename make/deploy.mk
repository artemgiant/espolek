# ============================================================================
# DEPLOY ТА СЕРВЕР (deploy.mk)
# ============================================================================

.PHONY: deploy-dev deploy-prod custom-dev

# ============================================================================
# ДЕПЛОЙ
# ============================================================================


deploy-dev: ## Деплой на DEV сервер
	@echo "Копіювання deploy.sh на DEV сервер..."
	@scp -i $(SSH_KEY_DEV) $(DEPLOY_SCRIPT_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV):$(SERVER_PATH_DEV)/
	@echo "Перевірка файлу на сервері..."
	@ssh -i $(SSH_KEY_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV) \
		'ls -la $(SERVER_PATH_DEV)/$$(basename $(DEPLOY_SCRIPT_DEV))'
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV) \
		'chmod +x $(SERVER_PATH_DEV)/$$(basename $(DEPLOY_SCRIPT_DEV))'
	@echo "Запуск деплою..."
	@ssh -i $(SSH_KEY_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV) \
		'cd $(SERVER_PATH_DEV) && ./$$(basename $(DEPLOY_SCRIPT_DEV))'
	@echo "Деплой DEV завершено!"



deploy-prod: ## Деплой на PROD сервер
	@echo "${YELLOW}⚠ УВАГА: Ви збираєтесь оновити ПРОДАКШН сервер!${RESET}"
	@echo "${YELLOW}Сервер: $(SERVER_HOST_PROD)${RESET}"
	@echo "${YELLOW}Шлях: $(SERVER_PATH_PROD)${RESET}"
	@read -p "Продовжити деплой на PROD? (y/n): " confirm && [ $$confirm = y ] || exit 1
	@echo "Копіювання deploy.sh на PROD сервер..."
	@scp -i $(SSH_KEY_PROD) $(DEPLOY_SCRIPT_PROD) $(SERVER_USER_PROD)@$(SERVER_HOST_PROD):$(SERVER_PATH_PROD)/
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY_PROD) $(SERVER_USER_PROD)@$(SERVER_HOST_PROD) \
		'chmod +x $(SERVER_PATH_PROD)/$$(basename $(DEPLOY_SCRIPT_PROD))'
	@echo "Запуск деплою..."
	@ssh -i $(SSH_KEY_PROD) $(SERVER_USER_PROD)@$(SERVER_HOST_PROD) \
		'cd $(SERVER_PATH_PROD) && ./$$(basename $(DEPLOY_SCRIPT_PROD))'
	@echo "${GREEN}✓ Деплой PROD завершено!${RESET}"




custom-dev: ## Запустити кастомний скрипт на DEV сервері
	@echo "Копіювання скрипту на DEV сервер..."
	@scp -i $(SSH_KEY_DEV) $(CUSTOM_SCRIPT_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV):$(SERVER_PATH_DEV)/
	@echo "Надання прав на виконання..."
	@ssh -i $(SSH_KEY_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV) \
		'chmod +x $(SERVER_PATH_DEV)/$$(basename $(CUSTOM_SCRIPT_DEV))'
	@echo "Запуск скрипту..."
	@ssh -i $(SSH_KEY_DEV) $(SERVER_USER_DEV)@$(SERVER_HOST_DEV) \
		'cd $(SERVER_PATH_DEV) && ./$$(basename $(CUSTOM_SCRIPT_DEV))'
	@echo "Скрипт виконано!"
