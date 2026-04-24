<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderHsmtChapter extends Model
{
    protected $table = 'tender_hsmt_chapters';

    protected $fillable = [
        'tender_id',
        'api_id',
        'code',
        'pcode',
        'name',
        'name_en',
        'description',
        'order_index',
        'level',
        'is_webform',
        'bid_form',
        'bid_field',
        'bid_file',
        'contract_type',
        'process_type',
        'raw',
    ];

    protected $casts = [
        'is_webform' => 'boolean',
        'raw' => 'array',
    ];

    
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    // parent
    public function parent()
    {
        return $this->belongsTo(self::class, 'pcode', 'code');
    }

    // children
    public function children()
    {
        return $this->hasMany(self::class, 'pcode', 'code')
            ->orderBy('order_index');
    }
}
