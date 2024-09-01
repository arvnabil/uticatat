<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'tanggal',
        'bank',
        'account_number',
        'nominal',
        'fee',
    ];
}
