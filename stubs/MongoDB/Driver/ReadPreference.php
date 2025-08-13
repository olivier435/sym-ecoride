<?php
namespace MongoDB\Driver;
class ReadPreference {
    public function __construct(int $mode, array $tagSets = [], array $options = []) {}
    public function getMode(): int {}
}
