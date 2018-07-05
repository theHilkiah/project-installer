<?php

namespace INST;

class ProjectInstaller
{
	private $argv = null;

	public function __construct($argv)
	{
		$this->argv = $argv;
	}
	
	public function run($handle)
	{
		$default = false;
		$dir = null;

		//$handle = fopen ("php://stdin","r");

		print "\n==================================================================================\n";
		print "\n\t";
		print "\t\ GIT REPOSITORY/PROJECT INSTALLER, v1.0";
		print "\n\t";
		print "\n==================================================================================\n";

		$argv = $this->argv;

		if(isset($argv[1]) && (strpos($argv[1], '-') === false)){

			$args = (strpos($argv[1], './') !== false)? $argv[1]: "./".$argv[1];

			$parts = preg_match("/([.\/]*)(\S+)/msi", trim($args), $matches); 

			$parent = realpath($matches[1]);

			if(!$parent) exit("\n\nThe path you entered is not valid. Parent directory doesn't exist!\n\r");

			print "Are you sure you want to clone a new repository in the following folder?:\n\n";
			$dir = $parent.DIRECTORY_SEPARATOR.trim($matches[2],'/\\');
			print "\t".$dir."\n\n";
			print "Please take a closer look above and confirm your entry ['Y' or 'N']:\n\n\tConfirm >>> ";

			if(strtoupper(trim(fgets($handle))) != 'Y')  exit("\nProcess cancelled, please try again!\n\n\r");

			print "\n";
		}
		if(isset($argv[1]) && (in_array('--help', $argv) ||  in_array('-h', $argv))){
			print "open this file to view the README content:\n\n\t".realpath(__DIR__.'/../README.md')."\n\n";
			exit();
		}
		if(!isset($argv[1]) || (!in_array('--skip', $argv) && !in_array('-s', $argv))){

			print "\nChecking for the latest version of this installer...\n";
			print "--------------------------------------------------------------------------------\n";
			shell_exec("composer global update thehilkiah/project-installer");
			print "--------------------------------------------------------------------------------\n";
			print "Checking of the latest project installer complete!\n\n";
			print "********************************************************************************\n\n";

		}
		if(isset($argv[1]) && (in_array('--default', $argv) ||  in_array('-d', $argv))){
			$default = true;
		}
		print "\n";

		$packages = require realpath(__DIR__."/../lib/packages.php");


		/***************************************************************************/

		print "Are you going to be using 'https' or 'ssh' for this?\n";
		print "(Leave blank or enter 0 for https, or enter 1 for ssh):\n";

		print "\n\tProtocol: >>> ";
		$protocol = trim(fgets($handle));
		if(stripos($protocol, 'https') !== false){
			$protocol = 0;
		}
		elseif(stripos($protocol, 'ssh') !== false) {
			$protocol = 1;
		}
		else{			
			$protocol  = $default? "https": ((int)trim(fgets($handle)) == 0? 'https': 'ssh');
		}
		if($protocol == 1){
			shell_exec("eval $(ssh-agent -s) && ssh-add ~/.ssh/id_rsa");
		}
		print "\n";

		$repo = null;
		$names = array();
		$clones = array();
		$mkdir = false;

		$index = 1;
		print "The following repositories (installable by this tool) were found!\n";
		foreach ($packages as $name => $package) {
			$names[$index]  = $name;
			$clones[$index] = $pack = $package[$protocol];
			echo "\n\t[$index]  $name - $pack";
			$index++;
		}

		print "\n\nWhat package would you like to clone?\n";
		print "(Type number in [n] to select; type 'x' to specify your own; or type 0 (or leave blank) to abort):\n";

		print "\n\tRepository: >>> ";
		$n  = (int)trim(fgets($handle));
		print "\n";

		$count = count($packages);

		switch ($n){
			case 0:
			exit("Wrong input, or you've selected to cancel this operation. Aborting!...\n\r");
			break;
		        
			case 'x':
			print "\n\tRepository: >>> ";
			$repo  = trim(fgets($handle));
			print "\n";
			break;

			case ($n <= $count):
			echo "You've selected the following package:\n\n";
			echo "\tURL: $names[$n]\n\tGIT: ".($repo = $clones[$n])."\n\n";
			break;

			case ($n > $count):
			exit("That package '[$n]' doesn't exist! Aborting!...\n");
			break;

			default:
			echo "Okay, nothing will be installed. Aborting!...\n";
			exit;
			break;
		}
		if(empty($repo)){
			exit("Sorry, you didn't select any repository! Aborting!...");
		}		
		if(empty($dir)){
			print "Where would you like to clone this repository to (relative to this folder)?: \n";
			print "\tType './' for this directory or '../' for parent of this directory to startn\n";
			print "\tThen type directory name you want to create. e.g. '../jumanji', or './jumanji'\n";
			print "\tOr leave it blank to use the default name of the repository for installation'\n";

			print "\n\tLocation: >>> ";
			$dir = $default? "": trim(fgets($handle));
			print "\n\n";

			if(empty($dir)){
				$parts = explode("/", $repo);
				$dir   = array_pop($parts);
				$dir   = './'.str_replace(".git", "", $dir);
			}
			else{
				$parts = preg_match("/([.\/]*)(\S+)/msi", trim($dir), $matches);
				$parent = realpath($matches[1]) ?: './'; 
				if(!$parent) exit("\n\nThe path you entered is not valid. Parent directory doesn't exist!\n\r");
				$dir = $parent.DIRECTORY_SEPARATOR.trim($matches[2],'/\\');

				$total = count(glob("$dir/*"));
				$exists = "";
				$notEmpty = " and, it is not empty!";

				if(file_exists($dir) || is_dir($dir))
					$exists .= "This directory already exists!";

				if($total > 0)
					$exists .= $notEmpty;

				if($exists != "" && strpos($exists, $notEmpty) !== false){
					rmdir($dir);
					exit("$exists\nPlease you a different directory name!\n\Aborting...\r");
				}
			}
		}
		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
			$mkdir = true;
		}
			
		print "Your repository will be cloned in:\n\n";
		print "\t".realpath($dir)."\n\n";

		print "Which branch are you going to clone into $dir?: \n";
		print "\tType the name of the branch, (e.g. '3.6.4, or 1.0-x')\n";
		print "\tOr, leave blank for the default branch, (usually master)\n";

		print "\n\tBranch: >>> ";
		if($default) 
			$branch = 'default';
		else
			$branch = trim(fgets($handle)) ?: 'default';
		print "\n";

		print "The '$branch' branch will be cloned into '$dir' direcotry\nIs it okay to continue? [please enter 'Y' or 'N' to confirm]: \n";

		print "\n\tConfirm: >>> ";
		$confirm = $default? "Y": trim(fgets($handle));
		print "\n";

		if(strtoupper($confirm) != 'Y'){
			if($mkdir && realpath($dir.'/.git')){
				rmdir(realpath($dir.'/.git'));
			}
			if($mkdir && realpath($dir)){
				rmdir(realpath($dir));
			}
			exit("Aborting...\n\r");
		}
		$b = (strpos($branch,'default') !== false)? '': "-b $branch ";		

		try {
			print "Cloning process starting...\n";
			print "--------------------------------------------------------------------------------\n";
			shell_exec("git clone $b$repo $dir && cd $dir && composer update");
			print "--------------------------------------------------------------------------------\n";
			print "Cloning process complete!\n\n";
			$site = 'localhost:'.($port = mt_rand(7777,9999));
			shell_exec("start \"\" localhost:$port && php -S $site");
			print "Your new repo is now available and the site is at $site";
			print "Thank you, Enjoy your repo!\n\n\r";
			shell_exec("cd $dir");
			print "\r";
		}
		catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}
		
	}
	
}
