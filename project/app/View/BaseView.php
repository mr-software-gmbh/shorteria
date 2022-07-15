<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\View;

use JetBrains\PhpStorm\NoReturn;

class BaseView
{
    protected string $layout;
    protected string $view;
    protected array $variables = [];
    private string $layoutHeader;
    private string $layoutFooter;
    private array $flashMessage = [];

    public function __construct()
    {
//        session_start();
        $this->layout = 'default';
        $this->layoutHeader = LAYOUT_PATH . $this->layout . DS . 'header.php';
        $this->layoutFooter = LAYOUT_PATH . $this->layout . DS . 'footer.php';
    }

    protected static function getInstance(): self
    {
        return new self();
    }

    /**
     * @throws \Exception
     */
    private function layoutException()
    {
        if (!file_exists($this->layoutHeader)) {
            throw new \Exception("$this->layoutHeader does not exist.");
        }

        if (!file_exists($this->layoutFooter)) {
            throw new \Exception("$this->layoutFooter does not exist.");
        }
    }

    public function setLayout($name)
    {
        $this->layout = $name . '.php';
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function compact($variables)
    {
        $this->variables = array_merge_recursive($variables);
    }

    /**
     * @throws \Exception
     */
    public function render($path): bool
    {
        $this->layoutException();

        $this->view = VIEW_PATH . $path . '.php';
        if (file_exists($this->view)) {
            extract(array_merge($this->flashMessage, $this->variables));
            ob_start();
            require $this->layoutHeader;
            include $this->view;
            require $this->layoutFooter;
            $output = ob_end_flush();

            /*
             * Clear flash message
             */
//            unset($_SESSION['flash']);
            $this->flashMessage = [];

            return $output;
        }
        throw new \Exception("$this->view does not exist.");
    }

    public function excerpt($string, $length, $endChar = '...')
    {
        $strLength = strlen($string);
        if ($strLength > $length) {
            $truncated = substr($string, 0, $length);
            $string = $truncated . $endChar;
        }

        return $string;
    }

    public function setFlash($message, $type = 'primary')
    {
//        $_SESSION['flash'] = [
//            'type'    => $type,
//            'message' => $message,
//        ];
        $this->flashMessage = [
            'flash_message' => [
                'type' => $type,
                'message' => $message,
            ],
        ];
    }

    public function referer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    #[NoReturn]
    public function redirect($path)
    {
        header("Location: $path", true, 301);
        exit;
    }
}
