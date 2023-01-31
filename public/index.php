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

// Получаем роутер – объект отвечающий за хранение и обработку  именнованых маршрутов
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

    $urls = $db->all();
    // Взять даты последней проверки url
    $lastCheck = [];
    foreach ($urls as $url) {
        $id = $url['id'];
        $lastTime = $db->findLastCheck($id);
        $created_at = $lastTime['created_at'] ?? null;
        $lastCheck[$id] = $created_at;
    }
    $params = ['urls' => $urls, 'lastCheck' => $lastCheck];
    return $this->get('renderer')->render($response, 'urls.phtml', $params);
})->setName('urls');

// Id Url Page
$app->get('/urls/{id:[0-9]+}', function ($request, $response, $args) use ($db) {

    $messages = $this->get('flash')->getMessages();
    $urlId = $args['id'];
    $urlData = $db->findUrl($urlId);
    $urlCheckData = $db->findCheckUrl($urlId);
    $params = ['url' => $urlData, 'urlCheck' => $urlCheckData, 'messages' => $messages];
    return $this->get('renderer')->render($response, 'url.phtml', $params);
})->setName('url');


// Add Url
$app->post('/urls', function ($request, $response) use ($router, $db) {
    $url = $request->getParsedBodyParam('url');
    // Валидация url
    $v = new UrlValidator;
    $errors = $v->validate($url);
    //Если ошибок нет, проверяем на существование страницы в БД
    if (count($errors) === 0) {

        $existsUrl = $db->findId($url['name']);

        if ($existsUrl) {
            $id = $existsUrl['id'];
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        } else { // добавляем новый url в бд
            $id = $db->insertUrl($url['name']);
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        }
            return $response->withRedirect($router->urlFor('url', ['id' => $id]));
    }

    $params = ['url' => $url, 'errors' => $errors];
    return $this->get('renderer')->render($response->withStatus(422), 'index.phtml', $params);
})->setName('addUrl');

// Check Url
$app->post('/urls/{id:[0-9]+}/checks', function ($request, $response, $args) use ($router, $db) {
    $urlId = $args['id'];
    $urlData = $db->findUrl($urlId);
    $urlCheckData = $db->addCheck($urlId);

    return $response->withRedirect($router->urlFor('url', ['id' => $urlId]));
})->setName('check');

$app->run();
