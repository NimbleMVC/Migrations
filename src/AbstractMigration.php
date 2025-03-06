<?php

namespace NimblePHP\Migrations;

use NimblePHP\Framework\Abstracts\AbstractModel;
use NimblePHP\Framework\Exception\NimbleException;
use NimblePHP\Framework\Exception\NotFoundException;
use NimblePHP\Framework\Interfaces\ControllerInterface;
use NimblePHP\Migrations\Interfaces\MigrationInterface;

/**
 * Abstract migration
 */
abstract class AbstractMigration implements MigrationInterface
{

    public ControllerInterface $controller;

    /**
     * Load model
     * @param string $name
     * @return AbstractModel
     * @throws NimbleException
     * @throws NotFoundException
     */
    public function loadModel(string $name): AbstractModel
    {
        $class = '\App\Model\\' . $name;

        if (!class_exists($class)) {
            throw new NotFoundException();
        }

        /** @var AbstractModel $model */
        $model = new $class();

        if (!$model instanceof AbstractModel) {
            throw new NimbleException('Failed load model');
        }

        $model->name = $name;
        $model->prepareTableInstance();
        $model->controller = $this->controller;

        return $model;
    }

}