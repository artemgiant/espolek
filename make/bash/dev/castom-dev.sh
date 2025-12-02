#!/bin/bash



#

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart horizon
sudo supervisorctl status


#php artisan clients:update
#
#php artisan equipment:update
#php artisan database:cleanup

 Подключение к Redis и очистка базы данных 0
redis-cli << EOF
SELECT 0
FLUSHDB
EOF

echo "Redis база данных 0 очищена"


# Очистка таблиц в MySQL базе данных

mysql -u admin -ppassword cms_gpscom << EOF
TRUNCATE TABLE equipment_sync_logs;
TRUNCATE TABLE failed_jobs;
TRUNCATE TABLE job_batches;

EOF

#echo "Все таблицы успешно очищены"


