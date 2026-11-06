<?php

namespace App\Enums;

enum MediaKind: string
{
    case Cover = 'cover';
    case Person = 'person';
    case Gallery = 'gallery';
    case Khqr = 'khqr';
    case Music = 'music';
    // A payment screenshot: private disk only, never given a public URL.
    case Proof = 'proof';
}
