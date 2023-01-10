<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\Flash\AbstractFlash;
use VitesseCms\Language\Services\LanguageService;

class FlashService
{
    /**
     * @var LanguageService
     */
    protected LanguageService $language;

    /**
     * @var AbstractFlash
     */
    protected AbstractFlash $flash;

    public function __construct(LanguageService $languageService, AbstractFlash $session)
    {
        $this->language = $languageService;
        $this->flash = $session;
    }

    public function setWarning(string $translation, array $replace = []): void
    {
        $this->flash->warning($this->language->get($translation, $replace));
    }

    public function setSucces(string $translation, array $replace = []): void
    {
        $this->flash->success($this->language->get($translation, $replace));
    }

    public function setNotice(string $translation, array $replace = []): void
    {
        $this->flash->notice($this->language->get($translation, $replace));
    }

    public function setError(string $translation, array $replace = []): void
    {
        $this->flash->error($this->language->get($translation, $replace));
    }

    public function output(): string
    {
        if (!$this->flash->has()):
            return '';
        endif;

        ob_start();
        $this->flash->output();
        $flash = ob_get_contents();
        ob_end_clean();

        return $this->language->parsePlaceholders($flash);
    }

    public function has($type = null): bool
    {
        return $this->flash->has($type);
    }
}
