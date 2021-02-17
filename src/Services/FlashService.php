<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\FlashInterface;
use VitesseCms\Language\Helpers\LanguageHelper;
use Phalcon\Flash\Session;
use VitesseCms\Language\Services\LanguageService;

class FlashService
{
    /**
     * @var LanguageService
     */
    protected $language;

    /**
     * @var Session
     */
    protected $session;

    public function __construct(LanguageService $languageService, Session $session)
    {
        $this->language = $languageService;
        $this->session = $session;
    }

    public function setWarning(string $translation, array $replace = []): void
    {
        $this->session->warning($this->language->get($translation, $replace));
    }

    public function setSucces(string $translation, array $replace = []): void
    {
        $this->session->success($this->language->get($translation, $replace));
    }

    public function setNotice(string $translation, array $replace = []): void
    {
        $this->session->notice($this->language->get($translation, $replace));
    }

    public function setError(string $translation, array $replace = []): void
    {
        $this->session->error($this->language->get($translation, $replace));
    }

    public function has($type = null): bool
    {
        return $this->session->has($type);
    }

    public function output(): string
    {
        if(!$this->session->has()):
            return '';
        endif;

        ob_start();
        $this->session->output();
        $flash = ob_get_contents();
        ob_end_clean();

        return $this->language->parsePlaceholders($flash);
    }
}
