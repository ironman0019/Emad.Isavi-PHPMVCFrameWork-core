<?php

namespace App\core;

use App\models\RegisterModal;

abstract class DbModel extends Model {

    abstract static public function tableName(): string;
    abstract static public function primaryKey(): string;
    abstract public function attributes(): array;
    abstract public function column(): array;
    abstract public function getDisplayName(): string;


    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $column = $this->column();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $stmt = self::prepare("INSERT INTO $tableName (".implode(',', $column).") 
            VALUES(".implode(',', $params).")
        ");

        foreach($attributes as $attribute) {
            
            $stmt->bindValue(":$attribute", $this->{$attribute});
        }

        $stmt->execute();
        return true;
        

    }

    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode('AND ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $stmt = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $stmt->bindValue(":$key", $item);
        }

        $stmt->execute();
        return $stmt->fetchObject(static::class);
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

}