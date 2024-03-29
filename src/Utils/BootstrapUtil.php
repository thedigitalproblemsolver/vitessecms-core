<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

use Phalcon\Autoload\Loader;

class BootstrapUtil
{
    public static function addModulesToLoader(Loader $loader, array $moduleDirs, string $account): Loader
    {
        foreach ($moduleDirs as $moduleDir) :
            $moduleDirParts = explode('/', $moduleDir);
            $moduleDirParts = array_reverse($moduleDirParts);
            $moduleNamespace = ucfirst($moduleDirParts[0]);
            if ($moduleNamespace === 'Src') :
                $moduleNamespace = ucfirst($moduleDirParts[1]);
            endif;
            if ($moduleDirParts[2] === $account) :
                $moduleNamespace = SystemUtil::createNamespaceFromPath($moduleDir . '/Module.php', false);
            endif;

            $loader->addDirectory($moduleDir);
            $loader->addNamespace('VitesseCms\\' . $moduleNamespace, $moduleDir);

            $subDirs = DirectoryUtil::getChildren($moduleDir);
            foreach ($subDirs as $subDir) :
                $subDirParts = explode('/', $subDir);
                $subDirParts = array_reverse($subDirParts);

                $namespace = 'VitesseCms\\' . $moduleNamespace . '\\' . ucfirst($subDirParts[0]);
                if ($subDirParts[3] === $account) :
                    $files = DirectoryUtil::getFilelist($subDir);
                    $namespace = SystemUtil::createNamespaceFromPath(array_key_first($files), false);
                endif;

                $loader->addDirectory($subDir);
                $loader->addNamespace($namespace, $subDir);
            endforeach;
        endforeach;

        return $loader;
    }
}
