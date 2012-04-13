<?php

require_once dirname(__FILE__).'/SymfonyRequirements.php';

$symfonyRequirements = new SymfonyRequirements();

$iniPath = $symfonyRequirements->getPhpIniConfigPath();

echo "***************************************\n";
echo "*                                     *\n";
echo "*  Comprobando requisitos de Symfony  *\n";
echo "*                                     *\n";
echo "***************************************\n\n";

echo $iniPath ? sprintf("* Archivo de configuraci\xf3n usado por PHP: %s\n\n", $iniPath) : "* ATENCI\xD3N: \xA1PHP no est\xc1 usando archivo de configuraci\xf3n (php.ini)!\n\n";

echo "** ATENCI\xD3N **\n";
echo "*  La CLI de PHP puede usar un archivo php.ini\n";
echo "*  diferente al usado por tu servidor web.\n";
if ('\\' == DIRECTORY_SEPARATOR) {
    echo "*  (especialmente en plataformas Windows)\n";
}
echo "*  Para estar en el lado seguro, por favor realiza la comprobaci\xf3n de requisitos\n";
echo "*  desde tu servidor web usando el archivo web/config.php.\n";

echo_title('Requisitos obligatorios');

foreach ($symfonyRequirements->getRequirements() as $req) {
    echo_requirement($req);
}

echo_title('Recomendaciones opcionales');

foreach ($symfonyRequirements->getRecommendations() as $req) {
    echo_requirement($req);
}

/**
 * Imprime una instancia de Requirement
 */
function echo_requirement(Requirement $requirement)
{
    $result = $requirement->isFulfilled() ? 'BIEN' : ($requirement->isOptional() ? "ATENCI\xD3N" : 'ERROR');
    echo ' ' . str_pad($result, 9);
    echo $requirement->getTestMessage() . "\n";

    if (!$requirement->isFulfilled()) {
        echo sprintf("          %s\n\n", $requirement->getHelpText());
    }
}

function echo_title($title)
{
    echo "\n** $title **\n\n";
}
