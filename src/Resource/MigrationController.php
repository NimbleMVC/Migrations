<?php

namespace Nimblephp\migrations\Resource;

use Nimblephp\framework\Abstracts\AbstractController;
use Nimblephp\framework\Interfaces\ControllerInterface;

class MigrationController extends AbstractController implements ControllerInterface
{

    public string $name = 'migration';

    public string $action = 'run';

}