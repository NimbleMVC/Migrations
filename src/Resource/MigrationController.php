<?php

namespace Nimblephp\migrations\Resource;

use Nimblephp\framework\Controller;
use Nimblephp\framework\Interfaces\ControllerInterface;

class MigrationController extends Controller implements ControllerInterface
{

    public string $name = 'migration';

    public string $action = 'run';

}