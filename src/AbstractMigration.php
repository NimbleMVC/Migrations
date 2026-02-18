<?php

namespace NimblePHP\Migrations;

use krzysztofzylka\DatabaseManager\DatabaseManager;
use NimblePHP\Framework\Interfaces\ControllerInterface;
use NimblePHP\Framework\Traits\LoadModelTrait;
use NimblePHP\Framework\Traits\LogTrait;
use NimblePHP\Migrations\Interfaces\MigrationInterface;
use PDO;

/**
 * Abstract migration
 */
abstract class AbstractMigration implements MigrationInterface
{

    use LogTrait;
    use LoadModelTrait;

    public ControllerInterface $controller;

    /**
     * Run query
     * @param string $query
     * @return array
     */
    public function query(string $query): array
    {
        return DatabaseManager::$connection->getConnection()->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

}