<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

use Phalcon\Di;
use Phalcon\DiInterface;
use VitesseCms\Configuration\Services\ConfigService;


class SystemUtil
{
    public static function getModules(ConfigService $configService): array
    {
        $return = [];
        $directories = [
            'rootdir' => $configService->getRootDir() . 'src',
            'accountdir' => $configService->getAccountDir() . 'src',
            'verdornamedir' => $configService->getVendorNameDir()
        ];

        foreach ($directories as $type => $directory) :
            if($type === 'verdornamedir') :
                $children = DirectoryUtil::getChildren($directory);
                unset($children['vitessecms']);
                foreach ($children as $key => $dir) :
                    $return[$key] = $dir.'/src';
                endforeach;
            else :
                $return = array_merge($return, DirectoryUtil::getChildren($directory));
            endif;
        endforeach;
        ksort($return);

        return $return;
    }

    public static function getModels(bool $namespaceAsKey = false): array
    {
        $return = [];

        foreach (SystemUtil::getModules(Di::getDefault()->get('configuration')) as $moduleName => $modulePath) :
            DirectoryUtil::getFilelist($modulePath . '/models');
            $return = array_merge($return, DirectoryUtil::getFilelist($modulePath . '/models'));
        endforeach;

        if ($namespaceAsKey) :
            $newReturn = [];
            foreach ($return as $filePath => $fileName) :
                $newReturn[SystemUtil::createNamespaceFromPath($filePath)] = $fileName;
            endforeach;
            $return = $newReturn;
        endif;

        return $return;
    }

    public static function createNamespaceFromPath(string $path): string
    {
        $handle = fopen($path, 'r');
        $ns = '';

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return $ns.'\\'.str_replace('.php','',(new \SplFileInfo($path))->getFilename());
    }

    public static function getFormclassFromClass(string $class): string
    {
        $classElements = [];
        $class = explode('\\', $class);
        $classElements[] = $class[0];
        $classElements[] = $class[1];
        $classElements[] = 'Forms';
        $class = array_reverse($class);
        $classElements[] = $class[0] . 'Form';

        return implode('\\', $classElements);
    }

    public static function loadClassFromNamespace($namespace): bool
    {
        $tmp = explode('\\',$namespace);
        $tmp = array_reverse($tmp);
        $className = $tmp[0];
        unset($tmp[0]);
        $tmp = array_reverse($tmp);
        unset($tmp[0]);

        $path = Di::getDefault()->get('config')->get('rootDir').'src/'.
            strtolower(implode('/',$tmp)) .'/'.$className. '.php';
        if(is_file($path)) :
            return include_once($path);
        endif;

        return false;
    }
}
