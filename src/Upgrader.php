<?php

namespace SKYW;

class Upgrader
{
  public function __construct()
  {
    $this->run();
  }
  
  public function run()
  {
    print "You are about to start upgrading code in this folder ".realpath('./')."\n";
    
    $this->checkForModules();
  }
  
  public function checkForModules($DIR)
  {
    $Modules = [];
    $dirs = glob("$DIR/*/composer.json");
    
    foreach($dirs as $dir){
      if(is_dir($dir){
        $this->checkForModules($dir);
        continue;
      }
      $Modules[] = [basename(dirname($dir)) = $dir];
    }
    if(count($Modules) > 0){
      $this->reorganizeModules($Modules);
    }
  }
  
  public function reorganizeModules($Modules)
  {
    $extras = [];
    foreach($Modules as $name => $module){
      $composerJson = file_get_contents($module.'/composer.json');
      $extras['installer-paths']['vendor/$name'][] = $composerJson['name'];
    }
  }
}
