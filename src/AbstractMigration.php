<?php

namespace Nimblephp\migrations;

use Nimblephp\framework\Abstracts\AbstractModel;
use Nimblephp\framework\Exception\NimbleException;
use Nimblephp\framework\Exception\NotFoundException;
use Nimblephp\framework\Interfaces\ControllerInterface;
use Nimblephp\migrations\Interfaces\MigrationInterface;

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
        $class = '\src\Model\\' . $name;

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