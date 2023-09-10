start:
	docker compose up -d
	docker exec systeme_test_job-php-1 composer install
	docker exec systeme_test_job-php-1 php bin/console doctrine:migrations:migrate
	echo y | docker exec -i systeme_test_job-php-1 php bin/console doctrine:fixtures:load
	docker exec -i systeme_test_job-php-1 echo y | npm install
	docker exec -i systeme_test_job-php-1 echo y | npm run watch





