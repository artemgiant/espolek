# ============================================================================
# ГОЛОВНИЙ MAKEFILE
# ============================================================================

.PHONY: help

# Кольори для виводу (tput)
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

# Кольори для виводу (ANSI)
BLUE := \033[0;34m
RED := \033[0;31m
NC := \033[0m

# ============================================================================
# ЗМІННІ СЕРВЕРА
# ============================================================================

# Dev сервер
SERVER_USER_DEV := root
SERVER_HOST_DEV := 172.104.230.129
SERVER_PATH_DEV := /var/www/espolek
SSH_KEY_DEV := ~/.ssh/id_rsa
DEPLOY_SCRIPT_DEV := make/bash/dev/deploy-dev.sh
CUSTOM_SCRIPT_DEV := make/bash/dev/castom-dev.sh

# Prod сервер
SERVER_USER_PROD :=
SERVER_HOST_PROD :=
SERVER_PATH_PROD :=
DEPLOY_SCRIPT_PROD :=
SSH_KEY_PROD :=

# ============================================================================
# ПІДКЛЮЧЕННЯ МОДУЛІВ
# ============================================================================

include make/docker.mk
include make/database.mk
include make/deps.mk
include make/queue.mk
include make/deploy.mk
include make/server.mk
include make/logs.mk
include make/testing.mk
include make/utils.mk

# ============================================================================
# ДОПОМОГА
# ============================================================================

help: ## Показати всі доступні команди
	@echo ''
	@echo '${GREEN}Використання:${RESET}'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<команда>${RESET}'
	@echo ''
	@echo '${GREEN}Команди:${RESET}'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-20s${GREEN}%s${RESET}\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ''

.DEFAULT_GOAL := help
