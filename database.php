<?php

namespace emadisavi\phpmvc;

class DataBase {

    public \PDO $pdo;

    public function __construct(array $config)
    {

        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $applyedMigrations =  $this->getApplyedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR. '/migrations');
        $toApplyMigrations = array_diff($files, $applyedMigrations);

        foreach ($toApplyMigrations as $migration) {

            if($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR. "/migrations/". $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instanse = new $className();
            $this->log("Applying migration $migration");
            $instanse->up();
            $this->log("Applyed migration $migration");
            $newMigrations[] = $migration;

        }

        if(!empty($newMigrations)) 
        {
            $this->saveMigrations($newMigrations);
        } 
        else
        {
            $this->log("All migrations have been applyed!");
        }


    }

    public function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)ENGINE=INNODB;";
        
        $this->pdo->exec($sql);
        
    }

    public function getApplyedMigrations()
    {
        $sql = "SELECT migration FROM migrations";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));

        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str;");
        $stmt->execute();
    }

    protected function log($massage)
    {
        echo '['.date('Y-m-d H:i:s'). '] - '.$massage. PHP_EOL;
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }





}