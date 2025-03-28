<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Helpers\LuhnHelper;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'account_name', 'account_number', 'account_type', 'currency', 'balance'];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->account_number = LuhnHelper::generateAccountNumber(12);
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
