<?php

namespace Nimblephp\migrations\Interfaces;

interface MigrationInterface
{

    /**
     * Script to run
     */
    public function run();

}