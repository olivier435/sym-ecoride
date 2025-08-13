<?php
namespace MongoDB\Driver;
class Cursor implements CursorInterface {
    public function toArray(): array {}
    public function setTypeMap(array $typemap): void {}
    public function getId(): int|string {}
}
