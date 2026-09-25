<?php

namespace TwintyLabs\InertiaPermission\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TwintyLabs\InertiaPermission\InertiaPermissionServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InertiaPermissionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }
}
