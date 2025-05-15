<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'content_title',
        'content_type',
        'content_path',
        'task_id',
        'owner_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relasi ke User sebagai pemilik dokumen
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
