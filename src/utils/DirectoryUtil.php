<?php

namespace VitesseCms\Core\Utils;

use Phalcon\Di;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use DirectoryIterator;

/**
 * Class DirectoryUtil
 */
class DirectoryUtil
{
    /**
     * recursive seach through a directory
     *
     * @param string $directory
     * @param string $pattern
     *
     * @return array
     */
    public static function recursiveSearch(string $directory, string $pattern): array
    {
        $dir = new RecursiveDirectoryIterator($directory);
        $ite = new RecursiveIteratorIterator($dir);
        $fileList = [];

        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        foreach ($files as $file) :
            if (is_file($file[0])) :
                $fileList[] = [
                    'path'      => $file[0],
                    'filemtime' => filemtime($file[0]),
                ];
            endif;
        endforeach;

        return $fileList;
    }

    /**
     * get direct child-directories of a directory
     *
     * @param string $directory
     *
     * @return array
     */
    public static function getChildren(string $directory): array
    {
        $dirs = [];
        if (is_dir($directory)) :
            $iterator = new DirectoryIterator($directory);
            foreach ($iterator as $fileinfo) :
                if ($fileinfo->isDir() && !$fileinfo->isDot()) :
                    $filename = $fileinfo->getFilename();
                    if (substr_count($fileinfo->getPathname(), 'account') > 0) :
                        $filename = Di::getDefault()->get('config')->get('account') . '\\' . $filename;
                    endif;
                    $dirs[$filename] = $fileinfo->getPathname();
                endif;
            endforeach;
            ksort($dirs);
        endif;

        return $dirs;
    }

    /**
     * get the filelist of a directory
     *
     * @param string $directory
     *
     * @return array
     */
    public static function getFilelist(string $directory): array
    {
        $files = [];
        if (is_dir($directory)) :
            $iterator = new DirectoryIterator($directory);
            foreach ($iterator as $fileinfo) :
                if (!$fileinfo->isDir() && !$fileinfo->isDot()) :
                    $files[$fileinfo->getPathname()] = $fileinfo->getFilename();
                endif;
            endforeach;
            ksort($files);
        endif;

        return $files;
    }

    /**
     * get the file and directorylist of a directory
     *
     * @param string $directory
     *
     * @return array
     */
    public static function getMixedList(string $directory): array
    {
        $files = [];
        if (is_dir($directory)) :
            $iterator = new DirectoryIterator($directory);
            foreach ($iterator as $fileinfo) :
                if (!$fileinfo->isDot()) :
                    $files[$fileinfo->getFilename()] = $fileinfo->getPathname();
                endif;
            endforeach;
            ksort($files);
        endif;

        return $files;
    }

    /**
     * @param string $path
     * @param bool $createIfNotExists
     *
     * @return bool
     */
    public static function exists(string $path, $createIfNotExists = false): bool
    {
        $state = is_dir($path);

        if ($createIfNotExists && !$state) :
            $pathReverse = explode('/', $path);
            $subPath = '';

            foreach ($pathReverse as $part) :
                $subPath .= $part.'/';
                if(!is_dir($subPath)) :
                    mkdir($subPath, 0755);
                endif;
            endforeach;

            $state = is_dir($path);
        endif;

        return $state;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public static function copy(string $source, string $destination): void
    {
        self::exists($destination, true);
        $dir = opendir($source);

        while(false !== ( $file = readdir($dir)) ) :
            if (
                $file != '.'
                && $file != '..'
                && !is_dir($source . '/' . $file)
            ) :
                copy($source . '/' . $file,$destination . '/' . $file);
            endif;
        endwhile;

        closedir($dir);
    }
}
