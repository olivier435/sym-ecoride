<?php
namespace MongoDB\BSON;
class Binary {
    public function __construct(string $data, int $type) {}
    public function getData(): string {}
    public function getType(): int {}
}
