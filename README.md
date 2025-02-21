## Description
An application created in the Laravel framework that integrates with the TMDB (The Movie Database) API. The aim of the project is to download data about films, series and genres from TMDB, save them in the database and provide its own REST API with multi-language support.
## Functions:
1. Data scraping:
Retrieving basic information about movies, series and genres from the TMDB API.
Saving the downloaded data to the database.
2. Multi-language:
Multi-language support (PL, EN, DE) for all three models:
3. REST API:
Creating endpoints that allow you to download data about films, series and genres depending on the language.
## Rules used
- Good programming practices, including SOLID,
- Error handling and exception logging when communicating with the API,
- Modularity and separation of logic into services.
## Requirements
- php 8.0 or higher
- laravel latest stable version
- composer
- mysql
- guzzlehttp
- api key

## Instalaltion
- add your api key in  example-app/.env (first line, next save the file)
- "DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tmdb_api
DB_USERNAME=root
DB_PASSWORD= " in .env file, next save the file and - 
Open terminal and :
- composer install
- composer require guzzlehttp/guzzle - download guzzlehttp library
- php artisan migrate
- php artisan serve
- php artisan fetch:tmdb-data to download data from TMDB
## Testing
After installing the application and fetching the data, you can test API by executing GET queries to these endpoints:

- /movies?lang=pl
- /movies?lang=en
- /series?lang=de
- /genres?lang=pl
