<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'jira_id',
        'name',
        'release_date',
        'released',
    ];

    public function issues()
    {
        return $this->belongsToMany(Issue::class);
    }
}
