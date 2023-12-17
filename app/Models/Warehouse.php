<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Warehouse extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'warehouseID',
        'warehouse_name',
        'warehouse_address',
        'warehouse_phone',
        'warehouse_manager_id',
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
    protected $primaryKey = 'warehouseID';

    /**
     * Get all transactions belong to this warehouse
     */
    public function transactions() {

        return $this->hasMany(Transaction::class, 'belongsTo', 'warehouseID');
    }

    protected $table = 'warehouse';
}
