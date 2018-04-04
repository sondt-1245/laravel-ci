<?php
use Symfony\Component\Yaml\Yaml;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('settings/oauth/clients', 'OAuthController@showClientsList')->name('oauth.clients');
Route::get('settings/oauth/authorized-clients', 'OAuthController@showAuthorizedClientsList')->name('oauth.authorized-clients');
Route::get('settings/oauth/personal-access-tokens', 'OAuthController@showPersonalAccessTokensList')->name('oauth.personal-access-tokens');

Route::resource('cities', 'CityController');
Route::resource('streets', 'StreetController');

Route::get('/yaml', function ()
{
    $yaml = <<<EOD
---
build:
  check:
    image: framgia/laravel-workspace
    commands:
      - curl -o /usr/bin/framgia-ci https://raw.githubusercontent.com/framgia/ci-report-tool/master/dist/framgia-ci && chmod +x /usr/bin/framgia-ci
      - chmod -R 777 storage/
      - chmod -R 777 bootstrap/cache/
      - cp .env.testing.example .env && cp .env.testing.example .env.testing
      - composer install
      - yarn
      - npm run production
      - php artisan migrate --database=mysql_test
      - framgia-ci run
compose:
  database:
    image: mysql
    environment:
      MYSQL_DATABASE: homestead_test
      MYSQL_USER: homestead_test
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: root
cache:
  mount:
    - .git
    - vendor
    - node_modules

EOD;

    $parsed = Yaml::parse($yaml);
    dd($parsed);
});

Route::get('/docker', 'HomeController@docker');
Route::get('/docker/image', 'HomeController@image');
