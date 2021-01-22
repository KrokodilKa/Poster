<?php
require_once dirname (__FILE__) . '/vendor/autoload.php';
use \VK\Client\VKApiClient;

$access_token = '9ed5ecc96978d4c39ecd6df0f337841cdc69bc148b9a36ec7a399cde7250c5944f5bb00e7d66d11e6f079';
$group_id = -183363397;
$vk = new VKApiClient();

//Путь к постам
$path_to_posts = dirname (__FILE__) . '/posts';

//Подсчёт количества постов
$dir = opendir($path_to_posts);
$count = 0;
while($file = readdir($dir)){
    if($file == '.' || $file == '..' || is_dir($path_to_posts . $file)){
        continue;
    }
    $count++;
};

//Нынешний пост
$current_post = ((int)file_get_contents('memory.txt') + 1);
//Рандомное число в диапазоне количества папок с постами
//$rand = rand(1, $count);
//Путь к папке с постом
$post_folder = $path_to_posts . '/' . $current_post;

//Получить список файлов из папки-поста
$post = scandir($post_folder);

//Массив поста и вложенности к нему
$params = ['owner_id' => array($group_id),];
$attachments = [];

foreach ($post as $value) {
    //Пропуск точек
    if($value == '.' || $value == '..' || is_dir($post_folder . $value)){
        continue;
    }
    //Работа с текстом если он есть 
    if (substr_count($value, '.txt') == 1) {
        $mess = (file_get_contents($post_folder . '/' . $value));
        $params['message'] = array($mess);
        continue;
    };

    //Работа с картинками

    //Получаем адрес для отправки фото
    $address = $vk->photos()->getWallUploadServer($access_token); 

    //Отправляем фото по указанному адресу
    $photo = $vk->getRequest()->upload($address['upload_url'], 'photo', $post_folder . '/' . $value); 

    //Сохранение фотографии
    $response_save_photo = $vk->photos()->saveWallPhoto($access_token, array(
    'server' => $photo['server'],
    'photo' => $photo['photo'],
    'hash' => $photo['hash'],
));
    //Заполнение attachments
    $load_photos = $response_save_photo[0];
    $attachments[] = "photo" . $load_photos['owner_id'] . "_" . $load_photos['id'] . ",";
};
//Разделение массива фотографий на единую строку
$attachments = array(implode(',',$attachments));
$params['attachments'] = $attachments;
//Цикл для заполнения 10 фото
// $x=0;
// while ($x++<10) {
//     $attachments[] = "photo" . $load_photos['owner_id'] . "_" . $load_photos['id'];//. ",";
// };

//Пост
$response = $vk->wall()->post($access_token, $params);

//Запись в память какой пост был последним
$last_post = ((int)file_get_contents('memory.txt') + 1);
file_put_contents('memory.txt', $last_post);
//Логгирование
Logger::getLogger($name)->log($data);
    //'attachments' => array(implode(',',!empty($attachments) ? $attachments : "")) , Для кучи фото
    //'attachments' => array(!empty($attachments) ? $attachments : ""),