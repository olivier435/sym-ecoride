<?php
namespace MongoDB\Driver;
class Manager {
    public function __construct(string $uri = "mongodb://127.0.0.1:27017", array $options = [], array $driverOptions = []) {}
    public function executeQuery(string $namespace, Query $query, ReadPreference $readPreference = null): Cursor {}
}
