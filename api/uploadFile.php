<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once('constants.php');
header("Access-Control-Allow-Origin: *");

class uploadFile{
  function Uploader_Image($user_id){
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return json_encode(array('status' => false , 'message' => 'Method Type Error.'));
      }
      $path=IMAGE_BASE_URL;//$path=$_SERVER['DOCUMENT_ROOT'] . "/ESS/api/uploads/users";
      if (isset($_FILES['uploadFile'])) { 
      $extension = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
      $originalName= $_FILES['uploadFile']['name'];
        if (!is_writable($path)) {
          echo json_encode(array(
            'status' => false,
            'message'    => 'Destination directory not writable.'
          ));
          exit;
        }
      $userFolderPath=$path.'/'.$user_id.'/'.date('Y').'/'.date('M').'/';
      if (!file_exists($userFolderPath)) {
          mkdir($userFolderPath, 0777, true);
      }
        //echo $userFolderPath;
        $generatedName =time().'.'.$extension;
        $filePath = $userFolderPath.$generatedName;
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $filePath)) {
          // echo json_encode(array(
          //   'status'        => true,
          //   'originalName'  => $originalName,
          //   'generatedName' => $generatedName
          // ));
          
          $tmp=$_FILES['uploadFile']['tmp_name'];
          $path=$filePath;
          $actual_image_name=$generatedName;
          
          // Create thumbnail here
          // $thumb = thumbnail($path, "64"); // image name, and size
          // imageToFile($thumb, $userFolderPath.'/thumb64_' . $generatedName . '.jpg');
          // // Create thumbnail here
          $thumb = thumbnail($path, "150"); // image name, and size
          imageToFile($thumb, $userFolderPath.'/thumb150_' . $generatedName . '.jpg');
          return true;
        }
      }
      else {
        echo json_encode(
          array('status' => false, 'message' => 'No file uploaded.')
        );
        exit;
    }
  }
  function Uploader_File($user_id){
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return json_encode(array('status' => false));
      }
      $path=IMAGE_BASE_URL;//$path=$_SERVER['DOCUMENT_ROOT'] . "/ESS/api/uploads/users";
      if (isset($_FILES['uploadFile'])) { 
      $extension = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
      $originalName= $_FILES['uploadFile']['name'];
        if (!is_writable($path)) {
          return json_encode(array(
            'status' => false,
            'message'    => 'Destination directory not writable.'
          ));
        }
      $userFolderPath=$path.'/'.$user_id.'/'.date('Y').'/'.date('M').'/';
      if (!file_exists($userFolderPath)) {
          mkdir($userFolderPath, 0777, true);
      }
        //echo $userFolderPath;
        $generatedName =time().'.'.$extension;
        $filePath = $userFolderPath.$generatedName;
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $filePath)) {
          // echo json_encode(array(
          //   'status'        => true,
          //   'originalName'  => $originalName,
          //   'generatedName' => $generatedName
          // ));
          
          $tmp=$_FILES['uploadFile']['tmp_name'];
          $path=$filePath;
          $actual_image_name=$generatedName;
          
          // Create thumbnail here
          // $thumb = thumbnail($path, "64"); // image name, and size
          // imageToFile($thumb, $userFolderPath.'/thumb64_' . $generatedName . '.jpg');
          // // Create thumbnail here
          $thumb = thumbnail($path, "150"); // image name, and size
          imageToFile($thumb, $userFolderPath.'/thumb150_' . $generatedName . '.jpg');

          return true;
        }
      }
      else {
        return json_encode(
          array('status' => false, 'msg' => 'No file uploaded.')
        );
    }
  }
  
  function thumbnail($inputFileName, $maxSize = 100){
    $info = getimagesize($inputFileName);
    $type = isset($info['type']) ? $info['type'] : $info[2];

    // Check support of file type
    if ( !(imagetypes() & $type) ){
      // Server does not support file type
      return false;
    }
    $width = isset($info['width']) ? $info['width'] : $info[0];
    $height = isset($info['height']) ? $info['height'] : $info[1];
    // Calculate aspect ratio
    $wRatio = $maxSize / $width;
    $hRatio = $maxSize / $height;
    // Using imagecreatefromstring will automatically detect the file type
    $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));
    // Calculate a proportional width and height no larger than the max size.
    if ( ($width <= $maxSize) && ($height <= $maxSize) ){
      // Input is smaller than thumbnail, do nothing
      return $sourceImage;
    }
    elseif ( ($wRatio * $height) < $maxSize ){
      // Image is horizontal
      $tHeight = ceil($wRatio * $height);
      $tWidth = $maxSize;
    }
    else{
      // Image is vertical
      $tWidth = ceil($hRatio * $width);
      $tHeight = $maxSize;
    }

    $thumb = imagecreatetruecolor($tWidth, $tHeight);
    if ( $sourceImage === false ){
      // Could not load image
      return false;
    }
    // Copy resampled makes a smooth thumbnail
    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
    imagedestroy($sourceImage);
    return $thumb;
  }
  
  function imageToFile($im, $fileName, $quality = 80){
    if ( !$im || file_exists($fileName) ){return false;}
    $ext = strtolower(substr($fileName, strrpos($fileName, '.')));
    switch ( $ext )
    {
      case '.gif':
        imagegif($im, $fileName);
        break;
      case '.jpg':
      case '.jpeg':
        imagejpeg($im, $fileName, $quality);
        break;
      case '.png':
        imagepng($im, $fileName);
        break;
      case '.bmp':
        imagewbmp($im, $fileName);
        break;
        default:
        return false;
    }
    return true;
  }
}
?>