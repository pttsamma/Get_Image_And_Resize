<?php

/*******************************************************
 * Author:  Sam Ma ( Taiwan / Taipei )
 * EMAIL:   pttsamma@gmail.com
 * Updated: 2017 Jun
 *
 * Brief Description:
 * It is a single file PHP script.
 * Download image from URL, resize it, and save.
 *******************************************************/


// Setting
/*******************************************************/
// Image Saving Path (Chmod 777)
$savePath = '/path_to_save/';
$site = 'http://your.website';

// Database
$servername = '127.0.0.1';
$username = 'username';
$password = 'password';
$dbname = 'db';
/*******************************************************/


/*******************************************************/

function GetImage($imageID, $imageURL) {

    // Set Resized Image 設定截圖大小
    $largeImageSize = [758, 426];
    $smallImageSize = [192, 108];

    // Unify Image Extension Name 統一圖片副檔名
    $imageExtension = end(explode('.', $imageURL));
    if ( strcasecmp($imageExtension, 'jpeg')==0 || strcasecmp($imageExtension, 'jpg')==0 ) {
        $imageExtension = 'jpg';
    }
    if ( strcasecmp($imageExtension, 'png')==0 ) {
        $imageExtension = 'png';
    }

    //Get Image Files and Save 抓取圖檔並儲存
    $imageName = 'pre/pre' . $imageID . '.' . $imageExtension;
    $image = file_get_contents($imageURL);
    if ($http_response_header != NULL) {
        $imageFile = $savePath . $imageName;
        file_put_contents($imageFile, $image);
    }

    switch ($imageExtension) {
        case 'jpg':
            header('Content-Type: image/jpeg');                                                                             // Content type
            list($width, $height) = getimagesize($imageName);                                                               // Get new sizes
            $source = imagecreatefromjpeg($imageName);                                                                      // Load
            $largeImage = imagecreatetruecolor($largeImageSize[0], $largeImageSize[1]);                                     // Load
            $smallImage = imagecreatetruecolor($smallImageSize[0], $smallImageSize[1]);                                     // Load
            imagecopyresized($largeImage, $source, 0, 0, 0, 0, $largeImageSize[0], $largeImageSize[1], $width, $height);    // Resize
            imagecopyresized($smallImage, $source, 0, 0, 0, 0, $smallImageSize[0], $smallImageSize[1], $width, $height);    // Resize
            imagejpeg($largeImage, $savePath . 'large/l' . $imageID . '.' . $imageExtension);                               // Save Large Image
            imagejpeg($smallImage, $savePath . 'small/s' . $imageID . '.' . $imageExtension);                               // Save Small Image
            break;
        case 'png':
            header('Content-Type: image/png');                                                                              // Content type
            list($width, $height) = getimagesize($imageName);                                                               // Get new sizes
            $source = imagecreatefrompng($imageName);                                                                       // Load
            $largeImage = imagecreatetruecolor($largeImageSize[0], $largeImageSize[1]);                                     // Load
            $smallImage = imagecreatetruecolor($smallImageSize[0], $smallImageSize[1]);                                     // Load
            imagecopyresized($largeImage, $source, 0, 0, 0, 0, $largeImageSize[0], $largeImageSize[1], $width, $height);    // Resize
            imagecopyresized($smallImage, $source, 0, 0, 0, 0, $smallImageSize[0], $smallImageSize[1], $width, $height);    // Resize
            imagepng($largeImage, $savePath . 'large/l' . $imageID . '.' . $imageExtension);                                // Save Large Image
            imagepng($smallImage, $savePath . 'small/s' . $imageID . '.' . $imageExtension);                                // Save Small Image
            break;
    }
}


// 取消執行時間
set_time_limit(0);

// DB Connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('資料庫連線失敗' . $conn->connect_error);
}

// Get Data From DB 從資料庫取得資料
$sql = 'SELECT id, image1_filename FROM posts';
$result = $conn->query($sql);

// Data Operation 處理資料
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        GetImage($row["id"], $site.$row["image1_filename"]);
    }
} else {
    echo "0 results";
}
$conn->close();
