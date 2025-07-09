<?php

namespace Aniket\LaravelWalletSystem\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model {
    protected $fillable = ['user_id', 'amount', 'type', 'description', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];
}
