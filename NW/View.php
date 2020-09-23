<?php
declare(strict_types=1);

namespace NW;

class View
{
    private array $variables = [];

    public function setVar(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    public function display(string $path): void
    {
        extract($this->variables, EXTR_OVERWRITE);

        ob_start();
        require $path;
        $content = ob_get_clean();
        require VIEW_PATH . '/Template/Default.phtml';
    }
}