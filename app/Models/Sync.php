<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Excel;
use App\Models\Settings;
use App\Models\Products;
use App\Models\Image;
use App\Models\Categories;
use App\Models\AttributeValues;
use App\Http\Controllers\ImagesController;
use Symfony\Component\DomCrawler\Crawler;
use \Dejurin\GoogleTranslateForFree;

class Sync extends Model
{
    use SoftDeletes;

    protected $storage = '../storage/sync/';
    protected $step_count = 100;
    protected $fillable = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'robots',
        'url_alias',
        'articul',
        'price',
        'old_price',
        'image',
        'gallery',
        'stock',
        'category',
        'attributes'
    ];

    protected $dates = ['deleted_at'];

    protected $table = 'sync';

	public $translations = [];

    protected $statuses = [
        'get_files' => 'getAllFiles',
        'parse_info' => 'parseInfoList',
        'parse_specifications' => 'parseSpecifications',
        'parse_inventory' => 'parseInventory',
        'import_products' => 'importProducts',
        'init_eros' => 'syncEros',
        'init_toystore' => 'getToystoreFile',
        'parse_toystore' => 'parseToystoreFile',
    ];

    public function initSync(){
        $settings = new Settings();
        $status = $settings->get_setting('sync_status');
        if(isset($this->statuses[$status])){
            $this->{$this->statuses[$status]}();
        }elseif(date('d.m.Y') == $status){
            $settings = new Settings();
            $settings->update_setting('sync_status', 'get_files', false);
        }elseif($status == (int)$status) {
            $this->importProducts((int)$status);
        }
    }

    public function getFile($path, $name){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_USERPWD, "sexshopintim@mail.ru:S0rSN");

        $options = array(CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 60);

        curl_setopt_array($ch, $options);
        $save = curl_exec($ch);
        curl_close($ch);

        file_put_contents($this->storage.$name,$save);
    }

    public function getAllFiles(){
        $files = [
            'info-list.csv' => 'http://tonga.com.ua/info/info-list.csv',
            'specifications.xls' => 'http://tonga.com.ua/info/specifications.xls',
            'ostatki.xls' => 'http://tonga.com.ua/info/ostatki.xls',
            'images.csv' => 'http://tonga.com.ua/info/images.csv'
        ];

        foreach ($files as $name => $path){
            $this->getFile($path, $name);
        }
        $settings = new Settings();
        $settings->update_setting('sync_status', 'parse_info', false);
    }

    public function parseXls($name){
        $data = Excel::load($this->storage.$name, function($reader) {})->get();

        return $data;
    }

    public function parseCsv($name){
        return $this->kama_parse_csv_file($this->storage.$name, 'cp1251', ';');
    }

    // Парсинг товаров
    public function parseInfoList(){
        $products = [];
        $infoList = $this->parseCsv('info-list.csv');
        foreach ($infoList as $product){
            if(!empty($product['v_products_model'])){
                $products[$product['v_products_model']] = [
                    'articul' => $product['v_products_model'],
                    'name' => $product['v_products_name_1'],
                    'description' => $product['v_products_description_1'],
                    'image' => $product['v_products_image'],
                    'price' => $product['v_products_price'],
                    'categories' => [$product['v_categories_name_1_1']],
                    'attributes' => [13 => $product['v_manufacturers_name']]
                ];
                $categories = [];
                for($i=1; $i<8; $i++){
                    if(!empty($product['v_categories_name_'.$i.'_1'])){
                        $categories[] = $product['v_categories_name_'.$i.'_1'];
                    }
                }
//                $products[$product['v_products_model']]['categories'] = implode(',', $categories);
                $products[$product['v_products_model']]['categories'] = $categories;
                $gallery = [];
                for($i=1; $i<5; $i++){
                    if(!empty($product['v_mo_image_'.$i])){
                        $gallery[] = $product['v_mo_image_'.$i];
                    }
                }
//                $products[$product['v_products_model']]['gallery'] = implode(',', $gallery);
                $products[$product['v_products_model']]['gallery'] = $gallery;
            }
        }

        $fp = fopen($this->storage.'parsed/products.json', "w");
        fwrite($fp, json_encode($products, JSON_UNESCAPED_UNICODE));
        fclose($fp);

//        $this->kama_create_csv_file( $products, $this->storage.'parsed/products.csv' );

        $settings = new Settings();
        $settings->update_setting('sync_status', 'parse_specifications', false);
    }

    // Парсинг атрибутов
    public function parseSpecifications(){
	    $settings = new Settings();
	    $settings->update_setting('sync_status', 'parse_inventory', false);
        $products = json_decode(file_get_contents($this->storage.'parsed/products.json'), true);
        $specifications = $this->parseXls('specifications.xls');
//        dd($specifications);

        if(isset($specifications[0])) {
            //        "АРТ" => "H44571"
            //        "ДИАМЕТР, см." => null
            //        "ДЛИНА, см." => null
            //        "ВИБРО" => null
            //        "МАТЕРИАЛ" => null
            //        "ЦВЕТ" => null
            //        "РАЗМЕР" => null
            //        "ЕМКОСТЬ, мл. или шт." => null
            //        "Количество в упак., шт." => "2 шт"
            //        "Инструкция" => "Ежедневно употреблять по одной капсуле, не разжевывая, с обильным количеством жидкости."
            //        "СОСТАВ" => "Экстракт корня кардамона, экстракт гуараны, витамин B6, витамин B12"
            //        "Тип батареи" => null
            //        " Дизайн" => null
            //        0 => null
            //        "Высота, мм" => null
            //        "Ширина, мм" => null
            //        "Толщина, мм" => null
            //        "Вес, г" => null
            //        "Страна" => "Австрия"
            //        "Группа скидок" => 2.0
            $attrs = [
                "ДИАМЕТР, см." => 5,
                "ДЛИНА, см." => 6,
                "МАТЕРИАЛ" => 11,
                "ЦВЕТ" => 3,
                "РАЗМЕР" => 14,
                "ЕМКОСТЬ, мл. или шт." => 18,
                "Количество в упак., шт." => 16,
                "СОСТАВ" => 15,
                "Тип батареи" => 8,
                "Страна" => 4
            ];
            foreach ($specifications[0] as $row) {
                if ($row->__isset('АРТ') && !empty($row->__get('АРТ')) && isset($products[$row->__get('АРТ')])) {
                    $attributes = $products[$row->__get('АРТ')]['attributes'];
                    foreach ($attrs as $key => $attr) {
                        if ($row->__isset($key) && !empty($row->__get($key))) {
                            $attributes[$attr] = $row->__get($key);
                        }
                    }
                    $products[$row->__get('АРТ')]['attributes'] = $attributes;
                }
            }

            $fp = fopen($this->storage.'parsed/products.json', "w");
            fwrite($fp, json_encode($products, JSON_UNESCAPED_UNICODE));
            fclose($fp);
        }
    }

    // Парсинг остатков
    public function parseInventory(){
        $products = json_decode(file_get_contents($this->storage.'parsed/products.json'), true);
        $inventory = $this->parseXls('ostatki.xls');
        foreach($inventory as $product){
            if(isset($products[$product['model']])){
                $products[$product['model']]['price'] = $product['price_EUR'];
                $products[$product['model']]['stock'] = empty($product['quantity']) ? 0 : 1;
            }
        }

        $fp = fopen($this->storage.'parsed/products.json', "w");
        fwrite($fp, json_encode($products, JSON_UNESCAPED_UNICODE));
        fclose($fp);

        $settings = new Settings();
        $settings->update_setting('sync_status', 'import_products', false);
    }

    public function ucfirst_utf8($str)
    {
        return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str)-1, 'utf-8');
    }

    // Импорт товаров
    public function importProducts($offset = 0){
        $count = $this->step_count;
        $products = json_decode(file_get_contents($this->storage.'parsed/products.json'), true);
        $settings = new Settings();
        $rate = $settings->get_setting('rate');

//        $categories = [];
//        foreach ($products as $sku => $product){
//            foreach ($product['categories'] as $cat){
//                if(!in_array($cat, $categories)){
//                    $categories[] = $cat;
//                }
//            }
//        }
//        foreach ($categories as $id => $cat){
//            $categories[$id] = $this->ucfirst_utf8(mb_strtolower($cat));
//        }
//        dd($categories);

        $i = 0;
        foreach ($products as $sku => $product){
            if($i >= $offset){
                $product['price'] = ((float)str_replace(',', '.', $product['price']))*$rate;
                if($this->issetProduct($sku, 'tonga.com.ua')){
                    echo $sku.': '.$product['name'].'<br>';
                    $this->updateProduct($product);
                }else{
                    echo '<b style="color: #f00">'.$sku.': </b>'.$product['name'].'<br>';
                    $product['source'] = 'tonga.com.ua';
                    $this->insertProduct($product);
                }
                $count--;
            }
            $i++;
            if($count == 0){
                break;
            }
        }

        if($count == $this->step_count){
            $settings = new Settings();
            $settings->update_setting('sync_status', 'init_eros', false);
        }else{
            $settings = new Settings();
            $settings->update_setting('sync_status', $i, false);
        }
    }

    // Проверка наличия товара
    public function issetProduct($sku, $source){
        $products = new Products();
        $product = $products->where('articul', $sku)->where('source', $source)->first();
        return $product;
    }

    // Обновление товара
    public function updateProduct($prod){
        $product_table_fill = [
//            'name' => $prod['name'],
//            'description' => $prod['description'],
            'price' => $prod['price'],
            'stock' => empty($prod['stock']) ? 0 : 1
        ];

//        if(!empty($prod['image'])){
//            $product_table_fill['image_id'] = $this->getImage($prod['image']);
//        }

        $product = Products::where('articul', $prod['articul'])->first();

//        $gallery_imagess = [];
//        foreach($prod['gallery'] as $img){
//            $gallery_imagess[] = $this->getImage($img);
//        }

//        if(is_null($product->gallery)){
//            $gallery = new Gallery();
//            $product_table_fill['gallery_id'] = $gallery->add_gallery($gallery_imagess);
//        }else{
//            $product->gallery->images = json_encode($gallery_imagess);
//        }

        $product->fill($product_table_fill);

        $product->push();

//        $cats = new Categories();
//        $categories = [];
//        foreach($prod['categories'] as $category){
//            $cat_name = $this->ucfirst_utf8(mb_strtolower($category));
//            $category = $cats->where('name', $cat_name)->first();
//            if(!empty($category)){
//                $categories[] = $category->id;
//            }else{
//                $categories[] = $cats->insertGetId([
//                    'name' => $cat_name,
//                    'meta_title'=> $cat_name,
//                    'url_alias' => strtolower($this->trim_value($this->rus2lat($cat_name))),
//                    'parent_id' => 5,
//                    'status' => 1
//                ]);
//            }
//        }
//        $product->categories()->sync($categories);

//        if (!empty($prod['attributes'])) {
//            $product_attributes = [];
//            $values = new AttributeValues();
//            foreach ($prod['attributes'] as $attribute => $value) {
//                $value = $values->where('attribute_id', $attribute)->where('name', $value)->first();
//                if(!empty($value)){
//                    $product_attributes[] = [
//                        'product_id' => $product->id,
//                        'attribute_id' => $attribute,
//                        'attribute_value_id' => $value->id,
//                    ];
//                }else{
//                    $product_attributes[] = [
//                        'product_id' => $product->id,
//                        'attribute_id' => $attribute,
//                        'attribute_value_id' => AttributeValues::insertGetId([
//                            'attribute_id' => $attribute,
//                            'name' => $value,
//                            'value' => strtolower($this->trim_value($this->rus2lat($value)))
//                        ])
//                    ];
//                }
//            }
//
//            $product->attributes()->delete();
//            $product->attributes()->createMany($product_attributes);
//            $product->create_product_thumnail($product->id);
//        }
    }

    // Создание товара
    public function insertProduct($prod){
        if(empty($prod['stock'])){
            return false;
        }

        $products = new Products();

        $product_table_fill = [
            'name' => $prod['name'],
            'description' => strip_tags($prod['description']),
            'meta_title'=> $prod['name'],
            'price' => $prod['price'],
            'stock' => $prod['stock'],
            'articul' => $prod['articul'],
            'url_alias' => $products->generate_alias(['name' => $prod['name']]),
            'source' => $prod['source']
        ];

        if(!empty($prod['image'])){
            $product_table_fill['image_id'] = $this->getImage($prod['image']);
        }

        $id = $products->insertGetId($product_table_fill);
        $product = $products->find($id);

        $gallery_imagess = [];
        foreach($prod['gallery'] as $img){
            $gallery_imagess[] = $this->getImage($img);
        }

        if(is_null($product->gallery)){
            $gallery = new Gallery();
            $product_table_fill['gallery_id'] = $gallery->add_gallery($gallery_imagess);
        }else{
            $product->gallery->images = json_encode($gallery_imagess);
        }

        $product->fill($product_table_fill);

        $product->push();

        $cats = new Categories();
        $categories = [];
        foreach($prod['categories'] as $category){
            $cat_name = $this->ucfirst_utf8(mb_strtolower($category));
            $category = $cats->where('name', $cat_name)->first();
            if(!empty($category)){
                $categories[] = $category->id;
            }else{
                $categories[] = $cats->insertGetId([
                    'name' => $cat_name,
                    'meta_title'=> $cat_name,
                    'url_alias' => strtolower($this->trim_value($this->rus2lat($cat_name))),
                    'parent_id' => 5,
                    'status' => 1
                ]);
            }
        }
//        $product->categories()->sync($categories);
        $product->categories()->attach($categories);

        if (!empty($prod['attributes'])) {
            $product_attributes = [];
            $values = new AttributeValues();
            foreach ($prod['attributes'] as $attribute => $value) {
                $value = $values->where('attribute_id', $attribute)->where('name', $value)->first();
                if(!empty($value)){
                    $product_attributes[] = [
                        'product_id' => $product->id,
                        'attribute_id' => $attribute,
                        'attribute_value_id' => $value->id,
                    ];
                }elseif(!empty($value)){
                    $product_attributes[] = [
                        'product_id' => $product->id,
                        'attribute_id' => $attribute,
                        'attribute_value_id' => AttributeValues::insertGetId([
                            'attribute_id' => $attribute,
                            'name' => $value,
                            'value' => strtolower($this->trim_value($this->rus2lat($value)))
                        ])
                    ];
                }
            }

//            $product->attributes()->createMany($product_attributes);
            $product->attributes()->createMany($product_attributes);
            $product->create_product_thumnail($product->id);

//            dd($product, $categories, $product_attributes);
        }
    }

    /**
     *  Удаление нежелательных символов
     *
     * @param $value
     * @return null|string|string[]
     */
    function trim_value($value)
    {
        if(is_string($value)) {
            $value = preg_replace('/(^"|"$|;$|\.$|,$|,\s?,)/', '', preg_replace('@^\s*|\s*$@u', '', $value));
        }
        return $value;
    }

    /**
     * Транслит
     * @param $string
     * @return mixed
     */
    public function rus2lat($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => "",  'ы' => 'y',   'ъ' => "",
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    // Получение id изображения
    public function getImage($image){
        $id = null;
        $img = Image::where('title', $image)->first();
        if(!empty($img)){
            return $img->id;
        }

        $url = 'http://tonga.com.ua/images/product_images/popup_images/'.$image;
        $file = new ImagesController(new Image());
        if(isset($file)){
            $file = $file->uploadFromUrlImages($url);
            if($file === false)
                $id = 0;
            else
                $id = $file->id;
        }

        return $id;
    }

    public function kama_parse_csv_file( $file_path, $file_encodings = ['cp1251','UTF-8'], $col_delimiter = '', $row_delimiter = "" ){

        if( ! file_exists($file_path) )
            return false;

        $cont = trim( file_get_contents( $file_path ) );

        if(is_array($file_encodings)){
            $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding($cont, $file_encodings) );
        }else{
            $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', $file_encodings );
        }

        unset( $cont );

        // определим разделитель
        if( ! $row_delimiter ){
            $row_delimiter = "\r\n";
            if( false === strpos($encoded_cont, "\r\n") )
                $row_delimiter = "\n";
        }

        $lines = explode( $row_delimiter, trim($encoded_cont) );
        $lines = array_filter( $lines );
        $lines = array_map( 'trim', $lines );

        // авто-определим разделитель из двух возможных: ';' или ','.
        // для расчета берем не больше 30 строк
        if( ! $col_delimiter ){
            $lines10 = array_slice( $lines, 0, 30 );

            // если в строке нет одного из разделителей, то значит другой точно он...
            foreach( $lines10 as $line ){
                if( ! strpos( $line, ',') ) $col_delimiter = ';';
                if( ! strpos( $line, ';') ) $col_delimiter = ',';

                if( $col_delimiter ) break;
            }

            // если первый способ не дал результатов, то погружаемся в задачу и считаем кол разделителей в каждой строке.
            // где больше одинаковых количеств найденного разделителя, тот и разделитель...
            if( ! $col_delimiter ){
                $delim_counts = array( ';'=>array(), ','=>array() );
                foreach( $lines10 as $line ){
                    $delim_counts[','][] = substr_count( $line, ',' );
                    $delim_counts[';'][] = substr_count( $line, ';' );
                }

                $delim_counts = array_map( 'array_filter', $delim_counts ); // уберем нули

                // кол-во одинаковых значений массива - это потенциальный разделитель
                $delim_counts = array_map( 'array_count_values', $delim_counts );

                $delim_counts = array_map( 'max', $delim_counts ); // берем только макс. значения вхождений

                if( $delim_counts[';'] === $delim_counts[','] )
                    return array('Не удалось определить разделитель колонок.');

                $col_delimiter = array_search( max($delim_counts), $delim_counts );
            }

        }

        $header = [];
        $data = [];
        foreach( $lines as $key => $line ){
            if(empty($header)){
                $header = str_getcsv( $line, $col_delimiter );
            }else{
                $d = str_getcsv( $line, $col_delimiter );
                if(count($header) == count($d)){
                    $data[] = array_combine($header, $d );
                }
            }
            unset( $lines[$key] );
        }

        return $data;
    }

    ## Создает CSV файл из переданных в массиве данных.
    ## @param array  $create_data  Массив данных из которых нужно созать CSV файл.
    ## @param string $file         Путь до файла 'path/to/test.csv'. Если не указать, то просто вернет результат.
    ## @return string/false        CSV строку или false, если не удалось создать файл.
    ## ver 2
    public function kama_create_csv_file( $create_data, $file = null, $col_delimiter = ';', $row_delimiter = "\r\n" ){

        if( ! is_array($create_data) )
            return false;

        if( $file && ! is_dir( dirname($file) ) )
            return false;

        // строка, которая будет записана в csv файл
        $CSV_str = '';

        // перебираем все данные
        foreach( $create_data as $row ){
            $cols = array();

            foreach( $row as $col_val ){
                // строки должны быть в кавычках ""
                // кавычки " внутри строк нужно предварить такой же кавычкой "
                if( $col_val && preg_match('/[",;\r\n]/', $col_val) ){
                    // поправим перенос строки
                    if( $row_delimiter === "\r\n" ){
                        $col_val = str_replace( "\r\n", '\n', $col_val );
                        $col_val = str_replace( "\r", '', $col_val );
                    }
                    elseif( $row_delimiter === "\n" ){
                        $col_val = str_replace( "\n", '\r', $col_val );
                        $col_val = str_replace( "\r\r", '\r', $col_val );
                    }

                    $col_val = str_replace( '"', '""', $col_val ); // предваряем "
                    $col_val = '"'. $col_val .'"'; // обрамляем в "
                }

                $cols[] = $col_val; // добавляем колонку в данные
            }

            $CSV_str .= implode( $col_delimiter, $cols ) . $row_delimiter; // добавляем строку в данные
        }

        $CSV_str = rtrim( $CSV_str, $row_delimiter );

        // задаем кодировку windows-1251 для строки
        if( $file ){
//            $CSV_str = iconv( "UTF-8", "cp1251",  $CSV_str );

            // создаем csv файл и записываем в него строку
            $done = file_put_contents( $file, $CSV_str );

            return $done ? $CSV_str : false;
        }

        return $CSV_str;

    }

    /**
     * Синхронизация с eros.com.ua
     */
    public function syncEros(){
        include(__DIR__.'/../../simple_html_dom.php');

        $products = new Products();
        $settings = new Settings();
        $categories = (array)$settings->get_setting('eros_categories');
        $no_categories = true;

        foreach($categories as $i => $category){
            if($category->parsed == 0){
                $no_categories = false;
                $categories[$i]->parsed = 1;

                $link = $this->qr_loadUrl($category->href);
                $html = str_get_html($link);
                if(empty($html)){
                	continue;
                }
                foreach ($html->find('.product-name') as $a){
                    if(!empty($a->href)){
                        $prod_href = $a->href;
                        $product_html = str_get_html($this->qr_loadUrl($prod_href));
                        $product_data = $this->parseErosProduct($product_html, trim($a->plaintext));
                        if(empty($product_data['articul'])){
                            continue;
                        }

                        if($this->issetProduct($product_data['articul'], 'eros.com.ua')){
                            echo $product_data['articul'].': '.$product_data['name'].'<br>';
                            $this->updateProduct($product_data);
                        }else{
                            echo '<b style="color: #f00">'.$product_data['articul'].': </b>'.$product_data['name'].'<br>';

                            $product_table_fill = [
                                'name' => $product_data['name'],
                                'description' => $product_data['description'],
                                'meta_title'=> $product_data['name'],
                                'price' => $product_data['price'],
                                'stock' => $product_data['stock'],
                                'articul' => $product_data['articul'],
                                'url_alias' => $products->generate_alias(['name' => $product_data['name']]),
                                'source' => $product_data['source'],
                                'image_id' => $product_data['image']
                            ];

                            $id = $products->insertGetId($product_table_fill);
                            $product = $products->find($id);

                            $gallery_imagess = $product_data['gallery'];

                            if(is_null($product->gallery)){
                                $gallery = new Gallery();
                                $product_table_fill['gallery_id'] = $gallery->add_gallery($gallery_imagess);
                            }else{
                                $product->gallery->images = json_encode($gallery_imagess);
                            }

                            $product->fill($product_table_fill);

                            $product->push();

                            $product->categories()->attach([$product_data['category']]);

                            if (!empty($product_data['attributes'])) {
                                $product_attributes = [];
                                foreach ($product_data['attributes'] as $attribute => $value) {
                                    $product_attributes[] = [
                                        'product_id' => $product->id,
                                        'attribute_id' => $attribute,
                                        'attribute_value_id' => $value,
                                    ];
                                }

                                $product->attributes()->createMany($product_attributes);
                                $product->create_product_thumnail($product->id);
                            }
                        }
                    }
                }

                $settings->update_setting('eros_categories', $categories, false);
                break;
            }
        }

        if($no_categories){
            foreach($categories as $i => $category){
                $categories[$i]->parsed = 0;
            }
            $settings->update_setting('eros_categories', $categories, false);
            $settings->update_setting('sync_status', 'init_toystore', false);
        }
    }

    public function parseErosProduct($html, $name){
        $attrs = [
            'Производитель' => 13,
            'Страна производитель' => 4,
            'Объем (мл/г)' => 18,
            'Запах' => 2,
            'Применение' => 17,
            'Длина полная (см)' => 6,
            'Длина рабочая (см)' => 7,
            'Диаметр (см)' => 5,
            'Материал' => 11,
            'Цвет' => 3,
            'Эл питание' => 8,
            'Основа' => 12,
            'Размер' => 14,
            'Состав' => 15,
            'Глубина (см)' => 10,
            'Вкус' => 9
        ];

        $category = '';
        foreach($html->find('.navigation_page a') as $a){
            if($category != ''){
                $category .= '>';
            }
            $category .= trim($a->plaintext);
        }
        $sku = trim($html->find('[itemprop="sku"]', 0)->plaintext);
        $price = trim(str_replace(' грн.', '', $html->find('[itemprop="price"]', 0)->plaintext));
        if(empty($sku)){
            $sku = trim($html->find('[itemprop="sku"]', 0)->content);
        }
        $product_data = [
            'name' => $name,
            'category' => $this->findOrCreateCategory($category),
            'articul' => $sku,
            'price' => (float)$price * 1.2,
            'image' => $this->findOrCreateImage($html->find('#thumbs_list_frame a', 0)->href),
            'description' => $html->find('[itemprop="description"]', 0)->plaintext,
            'source' => 'eros.com.ua',
            'stock' => 1,
            'gallery' => [],
            'attributes' => []
        ];
        foreach($html->find('#thumbs_list_frame a') as $a){
            $product_data['gallery'][] = $this->findOrCreateImage($a->href);
        }
//        $product_data['gallery'] = implode(',', $product_data['gallery']);
        $attributes = new AttributeValues();
        foreach($html->find('.page-product-box .table-data-sheet tr') as $tr){
            $name = $tr->find('td', 0)->plaintext;
            $val = $tr->find('td', 1)->plaintext;
            if(isset($attrs[$name])){
                $value = $attributes->select('id')->where('attribute_id', $attrs[$name])->where('name', '=', trim($val))->take(1)->get()->first();
                if(!empty($value)){
                    $val_id = $value->id;
                }else{
                    $val_id = $attributes->insertGetId([
                        'attribute_id' => $attrs[$name],
                        'name' => trim($val),
                        'value' => strtolower($this->trim_value($this->rus2lat($val)))
                    ]);
                }
                $product_data['attributes'][$attrs[$name]] = $val_id;
            }
        }

        return $product_data;
    }

    public function findOrCreateCategory($category){
        $categories = new Categories();
        $cat_tree = explode('>', $category);
        $id = 0;

        foreach($cat_tree as $cat_name){
            $cat = $categories->where('name', $cat_name)->first();
            if(empty($cat)){
                $id =$categories->insertGetId([
                    'name' => $cat_name,
                    'description' => null,
                    'sort_order' => 0,
                    'parent_id' => $id,
                    'image_id' => 0,
                    'meta_title' => $cat_name,
                    'status' => 1,
                    'url_alias' => strtolower($this->trim_value($this->rus2lat($cat_name)))
                ]);
            }else{
                $id = $cat->id;
            }
        }

        return $id;
    }

    public function findOrCreateImage($url){
        $images = new Image();
        $file = new ImagesController($images);
        $file = $file->uploadFromUrlImages($url);
        if($file === false)
            $id = 0;
        else
            $id = $file->id;

        return $id;
    }

    /**
     * Получение html страницы
     *
     * @param $url
     * @return mixed
     */
    public function qr_loadUrl( $url ) {
        if(is_callable( 'curl_init' )) {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($ch);
            curl_close($ch);
        }
        return $data;
    }

    public function getToystoreFile(){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	    curl_setopt($ch, CURLOPT_URL, 'http://toystore.com.pl/xmlapi/1/2/UTF8/4e90b630-218d-4971-91d7-e0342fa620fc');

	    $options = array(CURLOPT_FOLLOWLOCATION => 1,
	                     CURLOPT_TIMEOUT => 60);

	    curl_setopt_array($ch, $options);
	    $save = curl_exec($ch);
	    curl_close($ch);

	    file_put_contents($this->storage.'produkty.xml',$save);

	    $settings = new Settings();
	    $settings->update_setting('sync_status', 'parse_toystore', false);
    }

    public function parseToystoreFile(){
//	    $translations = $this->kama_parse_csv_file($this->storage.'parsed/translations.csv');
//	    foreach($translations as $translation){
//		    $this->translations[$translation['text']] = $translation['translate'];
//	    }
	    $xml = simplexml_load_string(file_get_contents($this->storage.'produkty.xml'));
	    $products = [];

//	    $attribute = new Attribute();
//	    $attributes = [];
//	    foreach ($attribute->all() as $attr){
//		    $attributes[$attr->name] = $attr->id;
//	    }

	    foreach($xml->product as $product){
//	    	$attrs = [];
//		    foreach($product->attributes->attribute as $attribute){
//		    	$name = $this->translate(trim((string)$attribute->name));
//		    	$label = $this->translate(trim((string)$attribute->label));
//			    if(isset($attributes[$name]))
//				    $attrs[$attributes[$name]] = $label;
//		    }
		    $products[] = [
		    	'articul' => (string)$product->sku,
//			    'attributes' => $attrs,
			    'price' => (float)str_replace(',', '.', (string)$product->priceAfterDiscountNet) * 86,
			    'stock' => (int)$product->qty ? 1 : 0
		    ];
//		    if(count($products) > 20){
//		    	break;
//		    }
	    }
//	    $translations = [['text']];
//	    foreach($this->translations as $text){
//		    $translations[] = [$text];
//	    }
//	    $this->kama_create_csv_file($translations, $this->storage.'parsed/translations.csv');
//	    dd($this->translations);
//	    dd($products);


	    foreach($products as $prod){
		    $product_table_fill = [
			    'price' => $prod['price'],
			    'stock' => $prod['stock']
		    ];
		    $product = Products::where('articul', $prod['articul'])->first();
		    if(!empty($product)){
			    $product->fill($product_table_fill);
			    $product->push();
		    }

//		    if (!empty($prod['attributes'])) {
//			    $product_attributes = [];
//			    $values = new AttributeValues();
//			    foreach ($prod['attributes'] as $attribute => $value) {
//				    $value = $values->where('attribute_id', $attribute)->where('name', $value)->first();
//				    if(!empty($value)){
//					    $product_attributes[] = [
//						    'product_id' => $product->id,
//						    'attribute_id' => $attribute,
//						    'attribute_value_id' => $value->id,
//					    ];
//				    }elseif(!empty($value)){
//					    $product_attributes[] = [
//						    'product_id' => $product->id,
//						    'attribute_id' => $attribute,
//						    'attribute_value_id' => AttributeValues::insertGetId([
//							    'attribute_id' => $attribute,
//							    'name' => $value,
//							    'value' => strtolower($this->trim_value($this->rus2lat($value)))
//						    ])
//					    ];
//				    }
//			    }
//
//			    $product->attributes()->createMany($product_attributes);
//		    }
	    }

	    $settings = new Settings();
	    $settings->update_setting('sync_status', date('d.m.Y', strtotime(date('d.m.Y')) +  86400), false);
    }

    public function translate($text){
    	if(isset($this->translations[$text])) {
		    return $this->translations[$text];
	    }else{
//		    $source = 'pl';
//		    $target = 'ru';
//		    $attempts = 5;
//
//		    $tr = new GoogleTranslateForFree();
//		    $result = $tr->translate($source, $target, $text, $attempts);
//
//		    if(!empty($result)){
//			    $this->translations[$text] = $result;
//			    return $result;
//		    }
	    }

	    return $text;
    }
}
