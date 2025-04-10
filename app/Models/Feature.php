<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'jira_id',
        'name',
        'description',
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class);
    }
}
