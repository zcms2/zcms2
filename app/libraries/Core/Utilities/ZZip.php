<?php

namespace ZCMS\Core\Utilities;

/**
 * Class ZZip
 *
 * @package ZCMS\Core\Utilities
 * @author ZCMS Team
 */
class ZZip
{

    /**
     * @var \ZipArchive
     */
    protected $zip;
    /**
     * @var array
     */
    protected $recursive = [];

    /**
     * @var \RecursiveIteratorIterator
     */
    protected $files;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var array
     */
    protected $notContains = [];

    /**
     * @var array
     */
    protected $needContains = [];

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var string
     */
    protected $zipRoot;

    /**
     * @var string
     */
    protected $baseDir;

    /**
     * Constructor
     *
     * @param null $rootPath
     * @param null $zipDes
     * @throws \Exception
     */
    public function __construct($rootPath = null, $zipDes = null)
    {
        if ($zipDes == null) {
            throw new \Exception('Zip file destination is empty!');
        }
        if ($rootPath == null || !is_dir($rootPath) || $zipDes == null) {
            throw new \Exception('Param $dir is null or $dir not exists!');
        }
        $this->zip = new \ZipArchive();

        if ($this->zip->open($zipDes, \ZipArchive::CREATE) != true) {
            throw new \Exception('Cannot open: ' . $zipDes);
        }

        $this->rootPath = $rootPath;

        $this->zipRoot = basename($this->rootPath);
        $this->baseDir = dirname($this->rootPath);//. '/';
        $this->zip->addEmptyDir($this->zipRoot);
        $this->files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }

    /**
     * Add file or folder if you don't want include that to zip
     *
     * @param $path
     */
    public function notContain($path)
    {
        $fullPath = $this->rootPath . DS . $path;
        if ($path && (file_exists($fullPath) || is_dir($fullPath))) {
            $this->notContains[] = $fullPath;
        }
    }

    /**
     * Zip Action
     *
     * @return bool
     */
    public function zip()
    {
        $recursive = [];
        foreach ($this->files as $name => $file) {
            $realPath = $file->getRealPath();
            //echo $name . '<br />';
            $baseName = basename($name);

            if ($baseName == '..' || $baseName == '.') {
                $folder = trim($realPath, '.');
                if ($this->checkNotContains($folder) == false) {
                    $recursive[] = [
                        'zFolder' => 1,
                        'fileRoot' => null,
                        'fileDes' => null,
                        'folder' => str_replace($this->baseDir, '', $folder)
                    ];
                }
            } else {
                if ($realPath && $this->checkNotContains($realPath) == false) {
                    $recursive[] = [
                        'zFile' => 1,
                        'fileRoot' => $realPath,
                        'fileDes' => str_replace($this->baseDir, '', $realPath),
                        'folder' => null
                    ];
                }
            }
        }
        $recursive = array_reverse($recursive);
        foreach ($recursive as $r) {
            if ($r['fileDes']) {
                $this->zip->addFile($r['fileRoot'], $r['fileDes']);
            } else {
                $this->zip->addEmptyDir($r['folder']);
            }
        }
        return $this->zip->close();
    }

    /**
     * Add file you want while that file in NotContains folder :D
     *
     * @param $fileName
     */
    public function addNeededContains($fileName)
    {
        $this->needContains[] = $fileName;
    }

    /**
     * Check add needed contains
     *
     * @param $fileName
     * @return bool
     */
    private function checkAddNeededContains($fileName)
    {
        foreach ($this->needContains as $n) {
            if (strpos($fileName, $n) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check file needed
     *
     * @param $fileName
     * @return bool
     */
    protected function checkNotContains($fileName)
    {
        if ($this->checkAddNeededContains($fileName) == true) {
            return false;
        }
        foreach ($this->notContains as $n) {
            if (strpos($fileName, $n) !== false) {
                return true;
            }
        }
        return false;
    }
}