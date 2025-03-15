<?php

namespace NimblePHP\Migrations\Resource;

use NimblePHP\Framework\Abstracts\AbstractController;
use NimblePHP\Framework\Interfaces\ControllerInterface;

class MigrationController extends AbstractController implements ControllerInterface
{

    public string $name = 'migration';

    public string $action = 'run';

}