<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use PageAnalyzer\Database\Repository;
use PageAnalyzer\urlValidator;
use Valitron\Validator;

//$sessionPath = __DIR__ . '/../temp/sessions/';
//session_save_path($sessionPath);
// Старт PHP сессии
session_start();

$db = new Repository();
//$t = $test->insertUrl('test12.com');

// Установка зависимостей в контейнер
$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();

// Home Page
$app->get('/', function ($request, $response) {

    $messages = $this->get('flash')->getMessages();
    $params = [
        'url' => [],
        'errors' => $messages
    ];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
})->setName('homepage');

// All Urls Page
$app->get('/urls', function ($request, $response) use ($db) {

    //$messages = $this->get('flash')->getMessages();
    $urls = $db->all();
    $params = ['url' => ''];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');


// Получаем роутер – объект отвечающий за хранение и обработку  именнованых маршрутов
$router = $app->getRouteCollector()->getRouteParser();

// Добавление url
$app->post('/urls', function ($request, $response) use ($router, $db) {
    $url = $request->getParsedBodyParam('url');
    // Валидация url
    $v = new UrlValidator;
    $errors = $v->validate($url);
    //Если ошибок нет, добавляем в БД и редирект на url
    if (count($errors) === 0) {
        $db->insertUrl($url['name']);
        return $response->withRedirect($router->urlFor('urls'));
    }

    $params = ['url' => $url, 'errors' => $errors];
    return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
})->setName('addUrl');

$app->run();
