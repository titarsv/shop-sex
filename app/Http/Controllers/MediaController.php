<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class MediaController extends Controller
{
    protected $images;
    protected $destinationPath = '/uploads';
    protected $destinationPathSmall = '/uploads/cache';

    public function __construct(Image $images)
    {
        $this->images = $images;
    }

    public function index(Request $request)
    {
        return view('admin.media.index');
    }

    public function upload(Request $request)
    {
        $file = $request->file('async-upload');
        $destinationPath = public_path().$this->destinationPath;

        $response = ['data' => []];

        //$newFileName = str_random(10).'.'.$file->guessExtension();
        $newFileName = $this->generate_filename($file, $destinationPath);
        $file->move($destinationPath, $newFileName);

        $image = new Image;
        $id = $image->insertGetId(
            ['title' => $file->getClientOriginalName(), 'href' => $newFileName]
        );

        $img = $image->get_image($id);
        $response['data'][] = $img;
        $img->update_image_sizes();

        $response['status'] = 200;

        $data = $img->getFullData();
        $response = [
            'success' => true,
            'data' => $data
        ];

        return response()->json($response);
    }

    /**
     * Генерация уникального имени файла
     *
     * @param $file
     * @param string $path
     * @return mixed
     */
    public function generate_filename($file, $path = ''){
        if(empty($path))
            $path = public_path().$this->destinationPath;

        $originalName = mb_strtolower(translit($file->getClientOriginalName()));

        if(is_file($path.'\\'.$originalName)) {
            $paths = explode('.', $originalName);
//            $extension = $file->extension();
            $extension = end($paths);
            $i = 2;
            $originalName = preg_replace('/(.+)(_\(\d+\))?\.'.$extension.'/', '$1_('.$i.').'.$extension, $originalName);
            while(is_file($path.'\\'.$originalName)){
                $originalName = preg_replace('/(.+)(_\(\d+?\))\.'.$extension.'/', '$1_('.$i.').'.$extension, $originalName);
                $i++;
            }
        }

        return $originalName;
    }

    public function convert_hr_to_bytes( $value ) {
        $value = strtolower( trim( $value ) );
        $bytes = (int) $value;

        if ( false !== strpos( $value, 'g' ) ) {
            $bytes *= 1024*1024*1024;
        } elseif ( false !== strpos( $value, 'm' ) ) {
            $bytes *= 1024*1024;
        } elseif ( false !== strpos( $value, 'k' ) ) {
            $bytes *= 1024;
        }

        // Deal with large (float) values which run into the maximum integer size.
        return min( $bytes, PHP_INT_MAX );
    }
}
