<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\AppSettingsUpdateRequest;
use App\Models\AppSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AppSettingController extends Controller
{
    public function index(): View
    {
        return view('cms.settings.index', [
            'settings' => AppSetting::query()
                ->orderBy('group')
                ->orderBy('label')
                ->get()
                ->groupBy('group'),
        ]);
    }

    public function update(AppSettingsUpdateRequest $request): RedirectResponse
    {
        $settings = AppSetting::query()->get()->keyBy('key');

        foreach ($settings as $key => $setting) {
            $value = $request->input('settings.'.$key);

            if ($setting->input_type === 'boolean') {
                $value = $request->boolean('settings.'.$key) ? '1' : '0';
            }

            if ($setting->input_type === 'upload_multiple' && $request->hasFile('setting_uploads.'.$key)) {
                $setting->clearMediaCollection('setting_asset');
                $files = is_array($request->file('setting_uploads.'.$key)) ? $request->file('setting_uploads.'.$key) : [$request->file('setting_uploads.'.$key)];

                foreach ($files as $file) {
                    $setting->addMedia($file)->toMediaCollection('setting_asset');
                }
                $value = null;
            } elseif ($setting->input_type === 'upload' && $request->hasFile('setting_uploads.'.$key)) {
                $setting->clearMediaCollection('setting_asset');
                $setting
                    ->addMedia($request->file('setting_uploads.'.$key))
                    ->toMediaCollection('setting_asset');

                $value = null;
            }

            $setting->update([
                'value' => is_array($value) ? json_encode($value) : $value,
            ]);
        }

        return redirect()
            ->route('cms.settings.index')
            ->with('status', 'App settings updated.');
    }
}