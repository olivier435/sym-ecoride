<?php
namespace MongoDB\Driver;
class BulkWrite {
    public function insert(array|object $document): mixed {}
    public function update(array|object $query, array|object $newObj, array $updateOptions = []): void {}
    public function delete(array|object $query, array $deleteOptions = []): void {}
}
