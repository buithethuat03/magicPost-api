<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transactionID',
        'transaction_name',
        'transaction_address',
        'transaction_phone',
        'transaction_manager_id',
        'belongsTo',
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
    
    ];

    /**
     * The primary key associated with the table.
     * 
     * @var array<string, string>
     */
    protected $primaryKey = 'transactionID';

    protected $table = 'transaction';
}
