<?php
namespace MongoDB\BSON;
class ObjectId {
    public function __construct(?string $id = null) {}
    public function __toString(): string {}
    public function getTimestamp(): int {}
}
