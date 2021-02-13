<?php

/**
 * @file
 * @category Blog
 * @package  Portfolio
 * @author   Russell Jones <jonesrussell42@gmail.com>
 * Database model for blog post from https://blog.jonesrussell42.xyz
 *
 * Bloggin
 */

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * @category Blog
 * @package  Portfolio
 * @author   Russell Jones <jonesrussell42@gmail.com>
 * Extend Eloquent
 *
 * test
 */
class Post extends Eloquent
{
    protected $connection = 'mongo';
    protected $collection = 'posts';
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
