<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity;
class Setting extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'settings';

    protected $appends = [
        'file',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'string' => 'string',
        'number' => 'number',
        'file'   => 'file',
        'select' => 'select',
    ];

    protected $fillable = [
        'key',
        'value',
        'name',
        'options',
        'lang',
        'type',
        'group_name',
        'order_level',
        'grid_col',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getFileAttribute()
    {
        return $this->getMedia('file')->last();
    }

    public static function getThemeSetting($key, $default = null)
    {
        $setting = self::where('key', $key)
            ->where('group_name', 'theme_settings')
            ->first();
        
        return $setting ? $setting->value : $default;
    }

    public static function getAllThemeSettings()
    {
        return self::where('group_name', 'theme_settings')
            ->pluck('value', 'key')
            ->toArray();
    }
}
