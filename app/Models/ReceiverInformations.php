<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReceiverInformations extends Model
{
    use HasFactory;

    protected $table = 'shipping_address';
    protected $guarded = [];

}
