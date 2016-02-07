<?php

require __DIR__ . "/vendor/autoload.php";

use App\Todo;
use App\TodoTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\DataArraySerializer;

date_default_timezone_set("UTC");

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$app = new Slim\App([
    "settings" => ["displayErrorDetails" => true]
]);

require __DIR__ . "/config/logger.php";
require __DIR__ . "/config/database.php";

$app->add(function ($request, $response, $next) {
    $response = $response
        ->withHeader("Access-Control-Allow-Headers", "Content-Type")
        ->withHeader("Access-Control-Allow-Methods", "GET,POST,PATCH,DELETE")
        ->withHeader("Access-Control-Allow-Origin", "*");
    return $next($request, $response);
});

$app->get("/", function ($request, $response, $arguments) {
    return $response->withStatus(301)
        ->withHeader("Location", "/todos");
});

$app->get("/todos", function ($request, $response, $arguments) {
    $todos = $this->spot->mapper("App\Todo")->all();

    $fractal = new Manager();
    $fractal->setSerializer(new ArraySerializer);
    $resource = new Collection($todos, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    /* Fractal collections are always namespaced. Apparently a feature and */
    /* not a bug. Thus we need to return $data["data"] for TodoMVC examples. */
    /* https://github.com/thephpleague/fractal/issues/110 */
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data["data"], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/todos", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $todo = new Todo($body);
    $this->spot->mapper("App\Todo")->save($todo);

    $fractal = new Manager();
    $fractal->setSerializer(new ArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/todos/{uuid}", function ($request, $response, $arguments) {
    $todo = $this->spot->mapper("App\Todo")->first(["uuid" => $arguments["uuid"]]);

    $fractal = new Manager();
    $fractal->setSerializer(new ArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/todos/{uuid}", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $todo = $this->spot->mapper("App\Todo")->first(["uuid" => $arguments["uuid"]]);
    $todo->data($body);
    $this->spot->mapper("App\Todo")->save($todo);

    $fractal = new Manager();
    $fractal->setSerializer(new ArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/todos/{uuid}", function ($request, $response, $arguments) {
    $todo = $this->spot->mapper("App\Todo")->first(["uuid" => $arguments["uuid"]]);
    $this->spot->mapper("App\Todo")->delete($todo);

    return $response->withStatus(204)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

/* In real life this this is probably a bad idea. */
$app->delete("/todos", function ($request, $response, $arguments) {
    $this->spot->mapper("App\Todo")->delete();
    return $response->withStatus(204);
});

$app->run();
