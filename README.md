# About
Web application for seraching restaurants. Application contains importing command for two different types of CSV import.
Currently opened restaurants are always on top of the list.
You can search restaurants by name, location or cuisine.

# Assigment
1. Given the attached CSV data files, write an artisan command/function importData, that will import data from CSVs to DB.

2. Create MVC to display imported data from DB
1. default view should display currently opened restaurant
2. we want to be able to search in restaurants (even closed ones)

Optimized solutions are nice, but the correct solutions are more important!
Assumptions:
* If a day of the week is not listed, the restaurant is closed on that day
* All times are local — don’t worry about timezone-awareness
* Don’t hesitate to be creative if you have time for it ;)

# Setup
Install dependencies:
```
./composer.phar install
yarn install
```

Build assets:
```
yarn build
```

Use docker-compose to build and run all required containers:
```
docker-compose up --build -d
```

Run Doctrine migrations:
```
docker exec restaurants_php-fpm_1 php bin/console doctrine:migrations:migrate --no-interaction
```

Import data from CSV:
```
docker exec -it restaurants_php-fpm_1 php bin/console app:import restaurants-hours-source-1.csv with-header
docker exec -it restaurants_php-fpm_1 php bin/console app:import restaurants-hours-source-2.csv headless
```

Access application on localhost:
```
http://127.0.0.1
```

# Doctrine
Create migration:
```
docker exec restaurants_php-fpm_1 php bin/console doctrine:migrations:diff
```

Run migrations:
```
docker exec restaurants_php-fpm_1 php bin/console doctrine:migrations:migrate
```