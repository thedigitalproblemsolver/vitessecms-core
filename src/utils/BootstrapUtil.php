<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

use Phalcon\Loader;

class BootstrapUtil
{
    public static function addModulesToLoader(
        Loader $loader,
        array $moduleDirs,
        string $account
    ): Loader {
        foreach ($moduleDirs as $moduleDir) :
            $moduleDirParts = explode('/', $moduleDir);
            $moduleDirParts = array_reverse($moduleDirParts);
            $moduleNamespace = ucfirst($moduleDirParts[0]);
            if($moduleNamespace === 'Src') :
                $moduleNamespace = ucfirst($moduleDirParts[1]);
            endif;
            if ($moduleDirParts[2] === $account) :
                $moduleNamespace = ucfirst($moduleDirParts[2]) . '\\' . $moduleNamespace;
            endif;

            $loader->registerDirs([$moduleDir], true);
            $loader->registerNamespaces(['VitesseCms\\' . $moduleNamespace => $moduleDir], true);

            $subDirs = DirectoryUtil::getChildren($moduleDir);
            foreach ($subDirs as $subDir) :
                $subDirParts = explode('/', $subDir);
                $subDirParts = array_reverse($subDirParts);
                $loader->registerDirs([$subDir], true);
                $loader->registerNamespaces(['VitesseCms\\' . $moduleNamespace . '\\' . ucfirst($subDirParts[0]) => $subDir], true);
            endforeach;
        endforeach;

        return $loader;
    }
}
