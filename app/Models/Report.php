<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @var list<string>
     */
    private const CATEGORIES = [
        'Pothole',
        'Broken streetlight',
        'Fly-tipping',
        'Blocked drain',
        'Graffiti',
        'Damaged road sign',
        'Other',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'description',
        'postcode',
        'address',
        'latitude',
        'longitude',
        'image_path',
        'status',
        'additional_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Report $report): void {
            if ($report->image_path) {
                Storage::disk('public')->delete($report->image_path);
            }
        });
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_SUBMITTED,
            self::STATUS_IN_REVIEW,
            self::STATUS_RESOLVED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function categories(): array
    {
        return self::CATEGORIES;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function orderedStatusHistory(): HasMany
    {
        return $this->statusHistory()->orderBy('created_at');
    }
}
