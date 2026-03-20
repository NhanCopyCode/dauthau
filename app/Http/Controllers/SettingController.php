<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = Setting::allConfigs();
        $tabs = array_keys($data);

        return view('settings.index', compact('data','tabs'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $settingConfigs = Setting::configsDefault();
        foreach ($settingConfigs as $sc){
            $setting = Setting::firstOrCreate(
                ['key' => $sc['key']],
                ['value' => $sc['value'],'description' => $sc['description']]
            );
            if($sc['type'] === 'image'){
                if($request->hasFile($sc['key'])) {
                    Setting::deleteFile($setting->value);
                    $value = Setting::saveImageResize( $request->file( $sc['key'] ) );
                    $setting->update(['value' => $value]);
                }
            }else{
                $setting->update(['value' => $request->get($sc['key'])]);
            }
        }
        return redirect('admin/settings')->with('success', trans('settings.update_success'));
    }
}
