<?php

if (!isset($_SERVER['HTTP_HOST'])) {
    exit('Este programa no se puede ejecutar desde la CLI. Ejecútalo desde un navegador.');
}

if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Prohibido');
    exit('Este programa únicamente es accesible a través de "localhost".');
}

require_once dirname(__FILE__).'/../app/SymfonyRequirements.php';

$symfonyRequirements = new SymfonyRequirements();

$majorProblems = $symfonyRequirements->getFailedRequirements();
$minorProblems = $symfonyRequirements->getFailedRecommendations();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title>Configurando Symfony</title>
        <link rel="stylesheet" href="bundles/framework/css/structure.css" media="all" />
        <link rel="stylesheet" href="bundles/framework/css/body.css" media="all" />
        <link rel="stylesheet" href="bundles/sensiodistribution/webconfigurator/css/install.css" media="all" />
    </head>
    <body>
        <div id="content">
            <div class="header clear-fix">
                <div class="header-logo">
                    <img src="bundles/framework/images/logo_symfony.png" alt="Symfony" />
                </div>

                <div class="search">
                  <form method="get" action="http://symfony.com/search">
                    <div class="form-row">

                      <label for="search-id">
                          <img src="bundles/framework/images/grey_magnifier.png" alt="Search on Symfony website" />
                      </label>

                      <input name="q" id="search-id" type="search" placeholder="Search on Symfony website" />

                      <button type="submit" class="sf-button">
                          <span class="border-l">
                            <span class="border-r">
                                <span class="btn-bg">OK</span>
                            </span>
                        </span>
                      </button>
                    </div>
                   </form>
                </div>
            </div>

            <div class="sf-reset">
                <div class="block">
                    <div class="symfony-block-content">
                        <h1 class="title">Welcome!</h1>
                        <p>Bienvenido a tu nuevo proyecto Symfony.</p>
                        <p>
                            Este programa te guiará a través de la configuración básica de tu proyecto.
                            También puedes hacer lo mismo editando el archivo ‘<strong>app/config/parameters.yml</strong>’ directamente.
                        </p>

                        <?php if (count($majorProblems)): ?>
                            <h2 class="ko">Problemas importantes</h2>
                            <p>Se han detectado problemas importantes y los <strong>debes</strong> solucionar antes de continuar:</p>
                            <ol>
                                <?php foreach ($majorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if (count($minorProblems)): ?>
                            <h2>Recomendaciones</h2>
                            <p>
                                <?php if (count($majorProblems)): ?>Adicionalmente, para<?php else: ?>Para<?php endif; ?> mejora tu experiencia con Symfony,
                                es recomendable que corrijas lo siguiente:
                            </p>
                            <ol>
                                <?php foreach ($minorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($symfonyRequirements->hasPhpIniConfigIssue()): ?>
                            <p id="phpini">*
                                <?php if ($symfonyRequirements->getPhpIniConfigPath()): ?>
                                    Los cambios al archivo <strong>php.ini</strong> se deben hacer en "<strong><?php echo $symfonyRequirements->getPhpIniConfigPath() ?></strong>".
                                <?php else: ?>
                                    Para cambiar las opciones, crea un "<strong>php.ini</strong>".
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!count($majorProblems) && !count($minorProblems)): ?>
                            <p class="ok">Tu configuración parece buena para ejecutar Symfony.</p>
                        <?php endif; ?>

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>
                                <li><a href="app_dev.php/_configurator/">Configura en línea tu aplicación Symfony</a></li>
                                <li><a href="app_dev.php/">Pospón la configuración y llévame a la página de bienvenida</a></li>
                            <?php endif; ?>
                            <?php if (count($majorProblems) || count($minorProblems)): ?>
                                <li><a href="config.php">Vuelve a probar</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="version">Edición estándar de Symfony</div>
        </div>
    </body>
</html>
