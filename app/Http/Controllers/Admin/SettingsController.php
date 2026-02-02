<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait; 
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Helpers\ThemeHelper;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $settings = Setting::with(['media'])->orderBy('order_level')->get()->groupBy('group_name'); 

        return view('admin.settings.index', compact('settings'));
    } 

    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        foreach ($request->all() as $key => $value) {
            $setting = Setting::where('key', $key)->where('lang', $request->lang)->first();
            if (!$setting) {
                $setting = Setting::where('key', $key)->first(); 
            } 

            if($setting){ 
                if($setting->type == 'file' && $request->input($setting->key)){
                    if ($request->input($setting->key, false)) {
                        if (! $setting->file || $request->input($setting->key) !== $setting->file->file_name) {
                            if ($setting->file) {
                                $setting->file->delete();
                            }
                            $setting->addMedia(storage_path('tmp/uploads/' . basename($request->input($setting->key))))->toMediaCollection('file');
                        }
                    } elseif ($setting->file) {
                        $setting->file->delete();
                    } 
                }else{
                    $setting->value = $value; 
                    $setting->save();
                }
                Cache::store('file')->forget('business_settings');
            }
        }
        return redirect()->route('admin.settings.index');
    }

    /**
     * Save theme settings to DB (all except direction and theme_mode — those are localStorage-only).
     */
    public function updateThemeSettings(Request $request)
    {
        $dbKeys = [
            'layout', 'width', 'header_style', 'menu_style', 'page_style',
            'header_position', 'menu_position', 'menu_behavior',
            'primary_color', 'background_color', 'background_light_color', 'loader',
        ];

        foreach ($dbKeys as $settingKey) {
            if ($request->has($settingKey)) {
                $setting = Setting::where('key', $settingKey)
                    ->where('group_name', 'theme_settings')
                    ->first();
                if ($setting) {
                    $setting->value = $request->input($settingKey);
                    $setting->save();
                }
            }
        }

        Cache::store('file')->forget('business_settings');

        return response()->json([
            'success' => true,
            'message' => 'Theme settings updated successfully',
        ]);
    }

    /**
     * Get theme settings from DB (for switcher; direction and theme_mode are localStorage-only).
     */
    public function getThemeSettings()
    {
        $settings = ThemeHelper::getThemeSettings();

        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('setting_create') && Gate::denies('setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Setting();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
