<?php

namespace App\Event;

use App\Entity\Complaint;
use Symfony\Contracts\EventDispatcher\Event;

class ComplaintSuccessEvent extends Event
{
    public const NAME = 'complaint.success';

    private Complaint $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function getComplaint(): Complaint
    {
        return $this->complaint;
    }
}