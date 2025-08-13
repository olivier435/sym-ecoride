<?php
namespace MongoDB\Driver;
class WriteConcern {
    public function __construct(string|int $w, int $wtimeout = 0, bool $journal = false) {}
    public function getW(): string|int {}
    public function getWtimeout(): int {}
    public function getJournal(): ?bool {}
}
