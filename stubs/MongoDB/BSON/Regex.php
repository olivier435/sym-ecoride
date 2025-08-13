<?php
namespace MongoDB\BSON;
class Regex {
    public function __construct(string $pattern, string $flags = '') {}
    public function getPattern(): string {}
    public function getFlags(): string {}
}
