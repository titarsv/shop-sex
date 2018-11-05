<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Modules;
use App\Http\Requests;
use App\Models\Settings;
use Validator;

class ModuleshopsController extends Controller
{

    protected $request;
    protected $slideshow;
    protected $modules;

    public $module_name = 'shops';

    public function __construct(Request $request, Modules $modules) {
        $this->request = $request;
        $this->modules = $modules;
    }

    public function index()
    {
        $module = $this->modules->getSlideshow($this->module_name);
        $settings = json_decode($module->settings);

        $slides = [];

        if(!empty($settings->slides)){
            foreach ($settings->slides as $slide){
                $slides[] = (object) [
                    'image_id' => $slide->image_id,
                    'slide_title' => $slide->slide_title,
                    'image' => Image::find($slide->image_id)
                ];
            }
        }

        $image_size = config('image.sizes.slide');

        return view('admin.modules.shops')
            ->with('module', $module)
            ->with('settings', $settings)
            ->with('image_size', $image_size)
            ->with('slideshow', $slides);
    }

    public function save()
    {
        $rules = [
            'slide.*.slide_title'           => 'required',
            'slide.*.image_id'           => 'required'
        ];
        $messages = [
            'slide.*.slide_title.required'           => 'Обязательно заполнить!',
            'slide.*.image_id.required'           => 'Обязательно заполнить!'
        ];

        $validator = Validator::make($this->request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $settings = json_encode([
            'slides'      => $this->request->slide
        ]);
        $update_param = [
            'settings' => $settings
        ];
        $this->modules->updateModule($this->module_name, $update_param);

        return redirect('admin/modules')
            ->with('modules', $this->modules->all())
            ->with('message-success', 'Настройки модуля Магазины успешно обновлены!');
    }
}
