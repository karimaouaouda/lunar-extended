<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\OrderLine;

class Attachment extends Model
{
    protected $fillable = [
        'order_line_id',
        'logo_heights',
        'logo_color',
        'logo',
        'printing_type',
        'status',
        'notes',
    ];




    /**
     * get the order line
    */
    public function order_line(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }
}
