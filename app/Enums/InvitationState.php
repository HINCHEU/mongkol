<?php

namespace App\Enums;

// Derived from the invitation's timestamps, never stored; see Invitation::state().
enum InvitationState: string
{
    case Trial = 'trial';
    case Paused = 'paused';
    case Live = 'live';
    case Ended = 'ended';
    case Suspended = 'suspended';
}
