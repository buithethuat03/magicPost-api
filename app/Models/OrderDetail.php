<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderDetail extends Model
{
    use HasFactory, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orderID',
        'sender_name',
        'sender_address',
        'sender_phone',
        'receiver_name',
        'receiver_address',
        'receiver_phone',
        'first_transaction_id',
        'last_transaction_id',
        'first_warehouse_id',
        'last_warehouse_id',
        'timeline',
        'weight',
        'shipping_fee',
        'orderType',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        //'timeline' => 'json',
        'timeline' => 'array'
    ];

    /**
     * The primary key associated with the table.
     * 
     * @var array<string, string>
     */
    protected $primaryKey = 'orderID';

    protected $table = 'order_details';
}
