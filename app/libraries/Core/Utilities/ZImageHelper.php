<?php

namespace ZCMS\Core\Utilities;

use Phalcon\Di;

/**
 * Class ZImageHelper
 *
 * @package ZCMS\Core\Utilities
 */
class ZImageHelper
{
    /**
     * @param \Phalcon\Http\Request\FileInterface[] $files
     * @param string $folderImages Eg: images => public/image
     * @param string $inputName
     * @param string $imageName
     * @param string $oldSource
     * @param bool $cryptName
     * @param int $maxFileSize Size with MB
     * @return array ['status' = true|false, 'message' = string|null, 'imageName' = string|null, 'imageUrl' => string|null, bugMessage = string|null]
     */
    public static function uploadImages($files, $folderImages, $inputName, $imageName = null, $oldSource = null, $cryptName = false, $maxFileSize = MAX_IMAGE_SIZE_UPLOAD)
    {
        $return = [
            'status' => false,
            'message' => null,
            'imageName' => null,
            'imageUrl' => null,
            'bugMessage' => null
        ];
        if (count($files) && !empty($inputName) && !empty($inputName) && !empty($folderImages)) {
            $folderImages = trim($folderImages, "/");
            foreach ($files as $file) {
                if (method_exists($file, 'getKey') && $file->getKey() == $inputName && $file->getName() != null) {
                    $fileName = $file->getName();
                    if (!$imageName) {
                        $imageTmpInfo = explode('.', $fileName);
                        $imageName = array_shift($imageTmpInfo);
                    }
                    $fileSize = $file->getSize();
                    $fileType = $file->getRealType();
                    $fileExtension = '.' . pathinfo($fileName)['extension'];

                    //Check file type
                    if (!self::checkFileType($fileType)) {
                        $return['status'] = false;
                        $return['message'] = __('gb_upload_image_failed_because_file_type_not_a_image');
                        return $return;
                    }

                    //Check file size
                    if ($fileSize > $maxFileSize * 1024 * 1024) {
                        $return['status'] = false;
                        $return['message'] = __('gb_upload_image_failed_because_image_size_too_large', [$maxFileSize]);
                        return $return;
                    }

                    //Crypt file name
                    $security = self::getSecurity();
                    if ($cryptName) {
                        $imageName = md5($security->hash($fileName . time()));
                    }

                    $newFile = $imageName . $fileExtension;

                    $i = 0;
                    while (file_exists(ROOT_PATH . '/public/' . $folderImages . DS . $newFile)) {
                        $newFile = $imageName . '_' . $i . $fileExtension;
                        $i++;
                    }

                    if (!is_dir($folderImages)) {
                        mkdir($folderImages, 0755, true);
                    }

                    if ($file->moveTo(ROOT_PATH . '/public/' . $folderImages . DS . $newFile)) {
                        $return['status'] = true;
                        $return['message'] = __('gb_upload_image_successfully');
                        $return['imageName'] = $imageName;
                        $return['imageUrl'] = DS . $folderImages . DS . $newFile;
                        if ($oldSource) {
                            $oldSource = ROOT_PATH . '/public/' . trim($oldSource, "/");
                            if (file_exists($oldSource) && self::checkFileType(mime_content_type($oldSource)) && strpos($oldSource, '/public/' . $folderImages . '/') !== false) {
                                unlink($oldSource);
                            }
                        }
                        return $return;
                    } else {
                        $return['status'] = false;
                        $return['message'] = __('gb_upload_image_failed');
                        $return['bugMessage'] = ROOT_PATH . DS . $folderImages . DS . $newFile;
                        $return['imageName'] = $imageName;
                        $return['imageUrl'] = DS . $folderImages . DS . $newFile;
                    }
                }
            }
        }
        $return['status'] = false;
        return $return;
    }

    /**
     * Check file type
     *
     * @param string $fileType
     * @return bool
     */
    public static function checkFileType($fileType)
    {
        //Check file type
        if (substr($fileType, 0, 5) != 'image') {
            return false;
        }
        return true;
    }

    /**
     * @return \Phalcon\Security
     */
    public static function getSecurity()
    {
        return DI::getDefault()->get('security');
    }
}