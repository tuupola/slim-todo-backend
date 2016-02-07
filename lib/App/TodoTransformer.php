<?php
namespace App;

use App\Todo;
use League\Fractal;

class TodoTransformer extends Fractal\TransformerAbstract
{

    public function transform(Todo $todo)
    {
        $utc = new \DateTimeZone("UTC");

        return [
            "uuid" => (string)$todo->uuid ?: null,
            "order" => (integer)$todo->order ?: 0,
            "title" => (string)$todo->title ?: null,
            "completed" => !!$todo->completed,
            "url" => getenv("BASE_URL") . "/todos/{$todo->uuid}"
        ];
    }
}