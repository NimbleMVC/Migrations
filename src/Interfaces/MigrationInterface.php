<?php

namespace NimblePHP\Migrations\Interfaces;

interface MigrationInterface
{

    /**
     * Script to run
     */
    public function run();

}