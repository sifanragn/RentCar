<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'charge'; 
    protected $primaryKey = 'invoice_id';
    public $timestamps = false;

    protected $fillable = [
        'rental_id',
        'tanggal_cetak',
        'total_tagihan',
        'status_pengembalian',
        'denda_tambahan',
        'total_akhir',
        'admin_id',
        'status_invoice',
    ];

    public function rental()
    {
        return $this->belongsTo(\App\Models\Rental::class, 'rental_id', 'rental_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}