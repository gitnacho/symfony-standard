<?php

namespace Acme\DemoBundle\Twig\Extension;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use CG\Core\ClassUtils;

class DemoExtension extends \Twig_Extension
{
    protected $loader;
    protected $controller;

    public function __construct(FilesystemLoader $loader)
    {
        $this->loader = $loader;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'code' => new \Twig_Function_Method($this, 'getCode', array('is_safe' => array('html'))),
        );
    }

    public function getCode($template)
    {
        // highlight_string highlights php code only if '<?php' tag is present.
        $controller = highlight_string("<?php" . $this->getControllerCode(), true);
        $controller = str_replace('<span style="color: #0000BB">&lt;?php&nbsp;&nbsp;&nbsp;&nbsp;</span>', '&nbsp;&nbsp;&nbsp;&nbsp;', $controller);

        $template = htmlspecialchars($this->getTemplateCode($template), ENT_QUOTES, 'UTF-8');

        // quita el bloque de código
        $template = str_replace('{% set code = code(_self) %}', '', $template);

        return <<<EOF
<p><strong>Código del controlador</strong></p>
<pre>$controller</pre>

<p><strong>Código de la plantilla</strong></p>
<pre>$template</pre>
EOF;
    }

    protected function getControllerCode()
    {
        $class = get_class($this->controller[0]);
        if (class_exists('CG\Core\ClassUtils')) {
            $class = ClassUtils::getUserClass($class);
        }

        $r = new \ReflectionClass($class);
        $m = $r->getMethod($this->controller[1]);

        $code = file($r->getFilename());

        return '    '.$m->getDocComment()."\n".implode('', array_slice($code, $m->getStartline() - 1, $m->getEndLine() - $m->getStartline() + 1));
    }

    protected function getTemplateCode($template)
    {
        return $this->loader->getSource($template->getTemplateName());
    }

    /**
     * Devuelve el nombre de la extensión.
     *
     * @return string El nombre de la extensión
     */
    public function getName()
    {
        return 'demo';
    }
}
