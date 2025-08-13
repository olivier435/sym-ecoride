<?php
namespace MongoDB\Driver;
class WriteResult {
    public function getInsertedCount(): int {}
    public function getMatchedCount(): int {}
    public function getModifiedCount(): int {}
    public function getDeletedCount(): int {}
}
