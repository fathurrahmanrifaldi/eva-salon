<?php

namespace App\Enums;

enum ActorType: string
{
    case Customer = 'customer';
    case Kasir = 'kasir';
    case Owner = 'owner';
    case System = 'system';
}
