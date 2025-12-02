# ============================================================================
# DEPLOY ТА СЕРВЕР
# ============================================================================

.PHONY: restart-php restart-nginx
.PHONY: horizon-setup supervisor-restart supervisor-status
.PHONY: horizon-logs-err horizon-logs-clear horizon-diagnose



# ============================================================================
# ПЕРЕЗАПУСК СЕРВІСІВ
# ============================================================================

restart-php: ## Перезапустити PHP-FPM на сервері
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'sudo systemctl restart php8.3-fpm'
	@echo "PHP-FPM перезапущено"

restart-nginx: ## Перезапустити Nginx на сервері
	@ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) \
		'sudo systemctl restart nginx'
	@echo "Nginx перезапущено"

# ============================================================================
# SUPERVISOR ТА HORIZON (REMOTE)
# ============================================================================

horizon-setup: ## Налаштування Horizon на сервері (reread + update + start)
	@echo "$(BLUE)═══════════════════════════════════════════════════════════$(NC)"
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

supervisor-restart: ## Перезавантаження Supervisor на сервері
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '$(BLUE)► Перезавантаження Supervisor...$(NC)' && \
		sudo service supervisor restart && \
		echo '$(GREEN)✓ Supervisor перезавантажений$(NC)'"
	@echo ""

supervisor-status: ## Статус усіх Supervisor процесів
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	@echo "$(BLUE)► Статус усіх Supervisor процесів:$(NC)"
	@echo ""
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "sudo supervisorctl status"
	@echo ""

horizon-logs-err: ## Показати помилки Horizon (50 рядків)
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	@echo "$(BLUE)► Помилки Horizon (останні 50 рядків):$(NC)"
	@echo ""
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "tail -50 /var/log/supervisor/horizon-err.log"
	@echo ""

horizon-logs-clear: ## Очистка логів Horizon на сервері
	@echo "$(BLUE)► Підключення до сервера...$(NC)"
	ssh -i $(SSH_KEY) $(SERVER_USER)@$(SERVER_HOST) "cd $(SERVER_PATH) && \
		echo '$(BLUE)► Очистка логів...$(NC)' && \
		sudo truncate -s 0 /var/log/supervisor/horizon-out.log && \
		sudo truncate -s 0 /var/log/supervisor/horizon-err.log && \
		echo '$(GREEN)✓ Логи очищені$(NC)'"
	@echo ""

horizon-diagnose: ## Діагностика Horizon на сервері
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
