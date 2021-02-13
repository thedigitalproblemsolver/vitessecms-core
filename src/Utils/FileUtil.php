<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Media\Utils\MediaUtil;
use Phalcon\Di;
use Phalcon\Tag;
use Phalcon\Utils\Slug;
use SplFileInfo;

/**
 * TODO make really a static util without internal dependancies
 */
class FileUtil
{
    /**
     * @var  SplFileInfo
     */
    public static $file;

    /**
     * @var array
     */
    protected static $abstractAdminControllerFunctions;

    public static function setFile(string $file = null): void
    {
        if ($file !== null) :
            self::$file = new SplFileInfo($file);
        endif;
    }

    public static function getExtension(string $file = null): string
    {
        self::setFile($file);

        return self::$file->getExtension();
    }

    public static function getName(string $file = null): string
    {
        self::setFile($file);

        return self::$file->getBasename('.'.self::getExtension());
    }

    public static function sanatize(string $file = null): string
    {
        self::setFile($file);

        return Slug::generate(
                self::$file->getBasename('.'.self::getExtension())
            ).'.'.Slug::generate(self::getExtension());
    }

    public static function getTag(string $file = null, string $class = null, string $style = null): string
    {
        self::setFile($file);

        /** @noinspection PhpUndefinedMethodInspection */
        $config = Di::getDefault()->getConfig();
        $fileRoot = $config->get('uploadDir').$file;
        $fileWeb = str_replace($config->get('webDir'), '', $fileRoot);

        if (is_file($fileRoot)) :
            switch (self::getExtension($file)) :
                case 'png':
                case 'jpg':
                case 'jpeg':
                case 'gif':
                    return Tag::image([
                        $fileWeb,
                        'alt'   => self::getName(),
                        'class' => $class,
                        'style' => $style,
                    ]);
                    break;
            endswitch;
        endif;

        return '';
    }

    public static function getContent(string $file = null): string
    {
        self::setFile($file);

        return file_get_contents($file);
    }

    public static function getFunctions(string $file, ConfigService $configuration): array
    {
        self::setFile($file);
        $exclude = ['initialize', 'onConstruct'];

        //TODO kan dit ook met class reflection?
        $functionFinder = '/function[\s\n]+(\S+Action)[\s\n]*\(/';
        $fileContents = file_get_contents(self::$file->getRealPath());
        preg_match_all($functionFinder, $fileContents, $functionArray);

        //TODO kan dit ook met class reflection?
        if (substr_count($fileContents, 'AbstractAdminController') > 0) :
            if (!isset(self::$abstractAdminControllerFunctions)) :
                $fileContents = file_get_contents(
                    $configuration->getVendorNameDir().'admin/src/AbstractAdminController.php'
                );
                preg_match_all($functionFinder, $fileContents, self::$abstractAdminControllerFunctions);
            endif;
            $functionArray[1] = array_merge($functionArray[1], self::$abstractAdminControllerFunctions[1]);
        endif;

        $return = [];
        foreach ($functionArray[1] as $function) :
            if (!in_array($function, $exclude, true)) :
                $return[] = $function;
            endif;
        endforeach;

        return $return;
    }

    public static function getSize(string $file = null): int
    {
        self::setFile($file);

        return self::$file->getSize();
    }

    public static function display(string $file = null): void
    {
        self::setFile($file);
        self::setHeaders();

        echo file_get_contents($file);
        die();
    }

    public static function setHeaders(string $file = null): void
    {
        self::setFile($file);

        Di::getDefault()->get('cache')->setCacheHeaders(
            filemtime(self::$file->getPathname()),
            filemtime(self::$file->getPathname())
        );

        if (
            isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime(self::$file->getPathname())
        ) :
            header('HTTP/1.0 304 Not Modified');
            exit;
        endif;

        self::setExtensionHeader();
    }

    public static function setExtensionHeader(string $file = null): void
    {
        self::setFile($file);
        switch (self::getExtension()) :
            case 'csv':
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename='.self::getName().'.'.self::getExtension());
                break;
            case 'css':
                header('Content-type: text/css');
                break;
            case 'jpg';
            case 'jpeg';
                header('Content-Type: image/jpeg');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'pdf':
                header('Content-type:application/pdf');
                header('Content-Disposition: attachment; filename='.self::getName().'.'.self::getExtension());
                break;
            case 'png';
                header('Content-Type: image/png');
                break;
            case 'svg';
                header('Content-Type: image/svg+xml');
                break;
        endswitch;
    }

    public static function getFiletypeGroups(): array
    {
        return [
            'rasterizedImages' => 'Rasterized images',
            'vectorImages'     => 'Vector images',
        ];
    }

    public static function getFiletypesByGroup(string $group): array
    {
        switch ($group):
            case 'rasterizedImages':
                return [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ];
                break;
            case 'vectorImages':
                return [
                    'image/svg+xml',
                    'application/illustrator',
                    'application/postscript',
                ];
                break;
        endswitch;

        return [];
    }

    public static function urlToLocalMapper(string $url): string
    {
        $urlParts = parse_url($url);
        $di = Di::getDefault();

        if (!isset($urlParts['query'])) :
            $search = [
                $di->get('url')->getBaseUri(),
            ];
            $replace = [
                $di->get('config')->get('webDir'),
            ];

            return str_replace($search, $replace, $url);
        else:
            parse_str($urlParts['query'], $urlQuery);
            if (isset($urlQuery['h']) || isset($urlQuery['w'])) :
                $file = $di->get('config')->get('webDir').$urlParts['path'];

                return Di::getDefault()->get('config')->get('cacheDir').
                    'resized/'.
                    MediaUtil::getResizeFilename(
                        $file,
                        $urlQuery['w'] ?? 0,
                        $urlQuery['h'] ?? 0
                    );
            endif;
        endif;

        return $url;
    }

    public static function copy(string $source, string $target): bool
    {
        $path = pathinfo($target);
        if (!DirectoryUtil::exists($path['dirname'], true)) :
            return false;
        endif;

        return copy($source, $target);
    }
}
