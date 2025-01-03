<?php

namespace App\Enums;

enum AttachementStatus : string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';


    public static function values(){
        $values = [];

        foreach(static::cases() as $case){
            $values[] = $case->value;
        }

        return $values;
    }
}
