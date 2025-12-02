.PHONY: create-resource

create-resource:
	@if [ -z "$(MODEL)" ]; then \
		echo "Введіть назву моделі (без App\\Models\\):"; \
		read MODEL; \
	fi
	@if php -r "if (!class_exists('App\\\\Models\\\\$$MODEL')) exit(1);" ; then \
		echo "❌ Модель App\\Models\\$(MODEL) не знайдена!"; \
		exit 1; \
	else \
		echo "✅ Модель $(MODEL) існує. Створюємо ресурс..."; \
	./vendor/bin/sail	php artisan make:filament-resource $(MODEL); \
	fi
