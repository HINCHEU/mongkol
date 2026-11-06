<?php

namespace App\Enums;

enum MediaKind: string
{
    case Cover = 'cover';
    case Person = 'person';
    case Gallery = 'gallery';
    case Khqr = 'khqr';
    case Music = 'music';
}
