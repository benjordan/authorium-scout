<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'jira_key',
        'status',
        'summary',
        'description',
        'type',
        'priority',
        'fix_version',
        'size',
        'release_commit_status',
        'product_manager_id',
    ];

    public function getRouteKeyName()
    {
        return 'jira_key';
    }

    public function components()
    {
        return $this->belongsToMany(Feature::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function fixVersions()
    {
        return $this->belongsToMany(FixVersion::class);
    }

    public function productManager()
    {
        return $this->belongsTo(ProductManager::class);
    }
}
