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
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="bundles/sensiodistribution/webconfigurator/css/install.css" media="all" />
        <title>Configurando Symfony</title>
    </head>
    <body>
        <div id="symfony-wrapper">
            <div id="symfony-content">
                <div class="symfony-blocks-install">
                    <div class="symfony-block-logo">
                        <img src="bundles/sensiodistribution/webconfigurator/images/logo-big.gif" alt="Logo de Symfony" />
                    </div>

                    <div class="symfony-block-content">
                        <h1>¡Bienvenido!</h1>
                        <p>Bienvenido a tu nuevo proyecto Symfony.</p>
                        <p>
                            Este programa te guiará a través de la configuración básica de tu proyecto.
                            También puedes hacer lo mismo editando el archivo ‘<strong>app/config/parameters.yml</strong>’ directamente.
                        </p>

                        <?php if (count($majorProblems)): ?>
                            <h2>Problemas importantes</h2>
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

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>
                                <li><a href="app_dev.php/_configurator/">Configura en línea tu aplicación Symfony</a></li>
                                <li><a href="app_dev.php/">Pospón la configuración y llévame a la página de bienvenida</a></li>
                            <?php endif; ?>
                            <li><a href="config.php">Vuelve a probar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="version">Edición estándar de Symfony</div>
    </body>
</html>
