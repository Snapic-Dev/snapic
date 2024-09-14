<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analytic extends Model
{
    use SoftDeletes;

    protected $table = 'analytics';

    protected $fillable = [
        'subscriber_name', 'revenue', 'commission', 'profit', 'parent_id', 'date', 'subscribers'
    ];

    protected $dates = ['deleted_at'];

    public $timestamps = true;
}
