<?php

namespace Tests;


use Nolanos\LaravelDoctrineFactory\DoctrineFactory;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DoctrineFactory::useNamespace('Workbench\\Database\\Factories\\');
    }
}
