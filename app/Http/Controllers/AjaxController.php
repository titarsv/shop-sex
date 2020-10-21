<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Models\Image;

class AjaxController extends Controller
{

    public function index(Request $request)
    {
        if($request->action == 'query-attachments'){
            return $this->queryAttachments($request->toArray());
        }elseif($request->action == 'image-editor'){
            return $this->imageEditor($request->toArray());
        }elseif($request->action == 'imgedit-preview'){
            return $this->imgeditPreview($request->toArray());
        }elseif($request->action == 'delete-post'){
            $id = intval($request->id);
            return $this->deletePost($id);
        }elseif($request->action == 'save-attachment'){
            $id = intval($request->id);
            if($request->changes['status'] == 'trash'){
                return $this->deletePost($id);
            }
        }

        return response()->json(['success' => false]);
    }

    public function queryAttachments($request){
        $image = new Image();
        $response = ['success' => true];
        $orderby = 'date';
        $order = 'DESC';
        $posts_per_page = 40;
        $paged = 1;
        if(!empty($request['query'])){
            if(!empty($request['query']['orderby'])){
                $orderby = $request['query']['orderby'];
            }
            if(!empty($request['query']['order'])){
                $order = $request['query']['order'];
            }
            if(!empty($request['query']['posts_per_page'])){
                $posts_per_page = $request['query']['posts_per_page'];
            }
            if(!empty($request['query']['paged'])){
                $paged = $request['query']['paged'];
            }
        }
        if($orderby == 'date'){
            $orderby = 'created_at';
        }
        $offset = $posts_per_page * ($paged - 1);

        $query = $image->orderBy($orderby, $order)->offset($offset)->take($posts_per_page);

        if(!empty($request['query']['year']) && $request['query']['year'] != 'false' && !empty($request['query']['monthnum']) && $request['query']['monthnum'] != 'false'){
            $monthnum = $request['query']['monthnum'];
            $timfrom = $request['query']['year'].'-'.($monthnum < 10 ? '0'.$monthnum : $monthnum).'-01 00:00:00';
            if($request['query']['monthnum'] == 12){
                $timto = ($request['query']['year']+1).'-01-01 00:00:00';
            }else{
                $monthnum++;
                $timto = $request['query']['year'].'-'.($monthnum < 10 ? '0'.$monthnum : $monthnum).'-01 00:00:00';
            }
            $query->whereBetween('created_at',[$timfrom,$timto]);
        }

        // TODO: допилить сортировку по типу
//        if(!empty($request['query']['post_mime_type'])){
//
//        }

        if(!empty($request['query']['s'])){
            $query->where('title', 'like', '%'.$request['query']['s'].'%');
        }

        $images = $query->get();
        $data = [];
        foreach ($images as $img){
            $data[] = $img->getFullData();
        }

        $response['data'] = $data;
        return response()->json($response);
    }

    public function imageEditor($request){
        if(!empty($request['postid'])){
            $image = Image::find($request['postid']);
        }

        return view('admin.media.editor')
            ->with('image', $image)
            ->with('nonce', $request['_ajax_nonce']);
    }

    public function imgeditPreview($request) {
        $post_id = intval($request['postid']);
        if ( empty($post_id) )
            return '-1';

        $image = Image::find($post_id);
        $path = public_path() . '/uploads/' . $image->href;
        $data = $image->fileData();
        $im = $image->imagecreatefromfile($path);

        switch ( $data['mime'] ) {
            case 'image/jpeg':
                header( 'Content-Type: image/jpeg' );
                return imagejpeg( $im, null, 90 );
            case 'image/png':
                header( 'Content-Type: image/png' );
                return imagepng( $im );
            case 'image/gif':
                header( 'Content-Type: image/gif' );
                return imagegif( $im );
            default:
                return '';
        }
    }

    public function deletePost($id){
        if ( empty($id) )
            return '-1';
        $images = new Image();

        $image = $images->get_image($id);
        if ($image !== null) {
            $image_data = $image->toArray();
            $created_sizes = json_decode($image_data['sizes'], true);
            if (!is_array($created_sizes))
                $created_sizes = array();
            foreach ($created_sizes as $size => $data) {
                $path = public_path('uploads/' . $data['href']);
                if (is_file($path))
                    unlink($path);
            }

            $path = public_path('uploads/' . $image_data['href']);
            if (is_file($path))
                unlink($path);

            $images->remove_image($id);
            $results[] = ['id' => $id, 'status' => 'deleted', 'message' => 'Изображение удалено'];
            return 1;
        } else {
            $results[] = ['id' => $id, 'status' => 'error', 'message' => 'Изображение не найдено'];
            return 0;
        }
    }
}
