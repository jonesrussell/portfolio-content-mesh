<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $connection = 'mongo';
    protected $collection = 'pages';
    protected $primaryKey = '_id';
    public $incrementing  = false;

    protected $fillable = [
        '_id',
        'status',
        'title',
        'promote',
        'sticky',
        'metatag',
        'body',
        'date',
        'link',
        'category',
    ];
}
