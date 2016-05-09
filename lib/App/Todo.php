<?php

namespace App;

use Spot\EntityInterface;
use Spot\MapperInterface;
use Spot\EventEmitter;

use Tuupola\Base62;
use Psr\Log\LogLevel;

class Todo extends \Spot\Entity
{
    protected static $table = "todos";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "order" => ["type" => "integer", "unsigned" => true, "value" => 0],
            "uid" => ["type" => "string", "length" => 16],
            "title" => ["type" => "string", "length" => 255],
            "completed" => ["type" => "boolean", "value" => false]
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->uid = Base62::encode(random_bytes(9));
        });
    }
}