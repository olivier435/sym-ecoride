<?php
namespace MongoDB\BSON;
class UTCDateTime {
    public function __construct(int|string|\DateTimeInterface|null $milliseconds = null) {}
    public function toDateTime(): \DateTime {}
}
