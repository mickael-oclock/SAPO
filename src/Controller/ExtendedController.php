<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExtendedController extends AbstractController
{

    private const SUCCESS_FLASH_KEY = "success";
    private const WARNING_FLASH_KEY = "warning";
    private const ERROR_FLASH_KEY = "error";
    protected $context;
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->context = [];
        $this->translator = $translator;
    }

    protected function addToContext($key, $value)
    {
        $this->context[$key] = $value;
    }

    protected function addSuccessFlash($translatableKey)
    {
        $this->addFlash(self::SUCCESS_FLASH_KEY, $this->translator->trans($translatableKey));
    }

    protected function addErrorFlash($translatableKey)
    {
        $this->addFlash(self::ERROR_FLASH_KEY, $this->translator->trans($translatableKey));
    }

    protected function addWarningFlash($translatableKey)
    {
        $this->addFlash(self::WARNING_FLASH_KEY, $this->translator->trans($translatableKey));
    }

    protected function renderWithContext(string $view, Response $response = null): Response
    {
        return parent::render($view, $this->context, $response);
    }
}