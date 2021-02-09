<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ContactForm extends Eloquent
{
    protected $connection = 'mongo';
    protected $collection = 'contact';
    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'preferred_contact_method',
        'preferred_contact_time',
        'department',
        'message',
    ];
}
