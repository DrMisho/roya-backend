<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;
use Symfony\Component\Uid\Ulid;

class Cobon extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'cobon';

    protected $fillable = [
        'status',
        'cobon',
        'package_id',
    ];

    public function newUniqueId()
    {
        return Str::ulid()->toRfc4122();
    }

    protected function getUlidAttribute(): Ulid
    {
        return Ulid::fromString($this->attributes['id']);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
