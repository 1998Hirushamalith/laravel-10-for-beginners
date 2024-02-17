<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN     = 'open';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}

// create migration model controller