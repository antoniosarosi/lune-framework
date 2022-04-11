<?php

namespace Lune\View;

/**
 * Lune template engine.
 */
class LuneEngine implements View {
    /**
     * Directory where the views are located.
     *
     * @var string
     */
    private string $viewsDirectory;

    /**
     * Annotation used in layouts to mark where to put the view content.
     */
    protected $contentAnnotation = "@content";

    /**
     * Layout to use in case none was given.
     */
    protected $defaultLayout = "main";

    public function __construct(string $viewsDirectory) {
        $this->viewsDirectory = $viewsDirectory;
    }

    public function render(string $view, array $params = [], string $layout = null): string {
        $layout ??= $this->defaultLayout;
        $layoutContent = $this->renderLayout($layout);
        $viewContent = $this->renderView($view, $params);

        return str_replace($this->contentAnnotation, $viewContent, $layoutContent);
    }

    /**
     * Render layout only, without replacing annotations.
     *
     * @param $layout
     * @return string Rendered layout.
     */
    protected function renderLayout(string $layout): string {
        return $this->phpFileOutput("$this->viewsDirectory/layouts/{$layout}.php");
    }

    /**
     * Render view only, without replacing annotations.
     *
     * @param $view View to render.
     * @param string $params Parameters passed to view.
     * @return string Rendered view.
     */
    protected function renderView(string $view, array $params = []): string {
        return $this->phpFileOutput("$this->viewsDirectory/{$view}.php", $params);
    }

    /**
     * Process PHP file and get string output.
     *
     * @param string $phpFile
     * @param array $params Variables to be made available inside the file.
     * @return string Processed output.
     */
    protected function phpFileOutput(string $phpFile, array $params = []): string {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();

        include_once $phpFile;

        return ob_get_clean();
    }
}
