<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'name',
        'email',
        'avatar_url',
    ];

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
}
