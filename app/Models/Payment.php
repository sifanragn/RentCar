<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'rental_id',
        'gateway',
        'metode',
        'total_bayar',
        'status_pembayaran',
        'gateway_reference',
        'payment_token',
        'callback_status',
        'tanggal_bayar',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }
}
