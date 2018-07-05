# Overview
This package, 'project-installer' is created to easy the process of creating a new git repository from the command-line. Sometimes these git repositories are not submited to Packagist, so you can't really run those using composer require. This will make it easy to do so

## Requirements
To use this command you need to have composer installed on your machine. Instructions are here: https://getcomposer.org/download/

Also, although not required, since you are dealing with git, it is best to use Git-Bash for these commands, especially if you are running Windows. To download, head over to https://git-scm.com/

## Installation
At the command prompt type (or just copy and paste the line):

        composer global require thehilkiah/project-installer dev-master --prefer-source
  
This will install the "inst" command on the composer global space. When prompted follow the instructions to complete.
### !IMPORTANT
After installation, go to lib/packages and add all repositories you wish to run using this command

## Usage
Just type the following command to start the cloning process:
       
       inst
       
If you wish to specify a directory to speed things up just replace *\<dir\>* below with the name of the directory you wish to clone your new repository into.
        
        inst <dir>

By default, this command will first check if there is a newer version of this install and automatically install it. To skip this processs, you can issue a skip flag to the command to skip updating

        inst <dir> --skip
Or, in short,

        inst <dir> -s

Other flags you can use are:

       --help, or -h            show this page, or a help section for this 'skw' command
       --default or -d          run 'inst' command faster, using all the defaults at each prompt

## Authors
* Hilkiah Makemo                hilmak01@github.com

## License
Copyright (c) 2018, <copyright holder>
