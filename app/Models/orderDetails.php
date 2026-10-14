<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class orderDetails extends Model
{
    use hasFactory;

    protected $table = 'order_details';

    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}
