<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Translation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'article_id',
        'title',
        'content',
        'language'
    ];
    protected $dates = ['deleted_at'];
}
