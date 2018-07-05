<?php

if(!class_exists(INST\ProjectInstaller::class)){ 
  
  require_once realpath(__DIR__.'/../src/Installer.php');
  
}

$argv = $argv ?: $GLOBALS['argv'] ?: $_SERVER['argv'];

$Installer = new INST\ProjectInstaller($argv);

$handle = fopen ("php://stdin","r");

return $Installer->run( $handle );

fclose($handle);
