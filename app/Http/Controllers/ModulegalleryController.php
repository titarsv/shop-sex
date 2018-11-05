<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modules;
use App\Models\Modulegallery;
use App\Http\Requests;
use App\Models\Settings;
use Validator;

class ModulegalleryController extends Controller
{

    protected $request;
    protected $slideshow;
    protected $modules;

    public $module_name = 'gallery';

    public function __construct(Request $request, Modulegallery $gallery, Modules $modules) {
        $this->request = $request;
        $this->slideshow = $gallery;
        $this->modules = $modules;
    }

    public function index()
    {
        $module = $this->modules->getSlideshow($this->module_name);
        $settings = json_decode($module->settings);

        $image_size = config('image.sizes.slide');

        return view('admin.modules.gallery')
            ->with('module', $module)
            ->with('settings', $settings)
            ->with('image_size', $image_size)
            ->with('slideshow', $this->slideshow->all());
    }

    public function save()
    {
        $rules = [
            'slide.*.slide_name'           => 'required',
            'slide.*.slide_cat'           => 'required'
        ];
        $messages = [
            'slide.*.slide_name.required'           => 'Обязательно заполнить!',
            'slide.*.slide_cat.required'           => 'Обязательно заполнить!'
        ];

        $validator = Validator::make($this->request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $module = $this->modules->getSlideshow($this->module_name);

        $settings = json_encode([
                'quantity'      => $this->request->quantity
        ]);
        $update_param = [
            'status' => $this->request->status,
            'settings' => $settings
        ];
        $this->modules->updateModule($this->module_name, $update_param);


        $this->slideshow->truncate();

        if (!empty($this->request->slide)) {
            foreach ($this->request->slide as $slide) {
                if (!$slide['image_id']) {
                    $slide['image_id'] = 1;
                };
                $slide_data['slide_name'] = $slide['slide_name'];
                $slide_data['slide_cat'] = $slide['slide_cat'];
                $slide['slide_data'] = json_encode($slide_data);
                $this->slideshow->create($slide);
            }
        }

        return redirect('admin/modules')
            ->with('modules', $this->modules->all())
            ->with('message-success', 'Настройки модуля ' . $module->name . ' успешно обновлены!');
    }
}
