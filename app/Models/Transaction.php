<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'invoice_number',
        'receive_from',
        'patient_name',
        'optometrist_name',
        'pay_for',
        'frame_type',
        'frame_price',
        'lens_type',
        'lens_price',
        'total_price',
        'amount_in_words',
        'date',
    ];

    public $timestamps = true;
}
