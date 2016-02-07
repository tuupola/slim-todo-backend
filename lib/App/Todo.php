<?php

namespace App;

use Spot\EntityInterface;
use Spot\MapperInterface;
use Spot\EventEmitter;

use Ramsey\Uuid\Uuid;
use Psr\Log\LogLevel;

class Todo extends \Spot\Entity
{
    protected static $table = "todos";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "order" => ["type" => "integer", "unsigned" => true, "value" => 0],
            "uuid" => ["type" => "string", "length" => 36],
            "title" => ["type" => "string", "length" => 255],
            "completed" => ["type" => "boolean", "value" => false]
        ];
    }

    public static function relations(MapperInterface $mapper, EntityInterface $entity)
    {
        return [];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $uuid = Uuid::uuid1();
            $entity->uuid = $uuid->toString();
        });
    }
}