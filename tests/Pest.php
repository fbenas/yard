<?php

$moduleTestDirectories = array_map(
    fn ($path) => '../modules/' . basename(dirname($path)) . '/tests',
    glob(__DIR__ . '/../modules/*/tests') ?: []
);

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
    Tests\Helpers\Auth::class,
)->in('Feature', ...$moduleTestDirectories);

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something()
{
    // ..
}
