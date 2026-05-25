<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderHsmt extends Model
{
    protected $table = 'tender_hsmts';

    protected $guarded = [];

    protected $casts = [

        'view_json' => 'array',

        'raw_json' => 'array',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
