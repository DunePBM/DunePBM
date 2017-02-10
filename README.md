# DunePBM
A Dune Boardgame PBM in PHP.

This is designed to run a Play by Forum game of the Dune Boardgame,
using the WBC 2016 rules set.

## Setup Instructions
Right now I'm testing this on an Orange Pi with Apache2 and PHP 5.6.

You can have the dune_pbm and dune_pbm_data folders wherever you want, as 
long you set the $gamePath /var/www/html/index.php to the location of 
dune_pbm in and set $dataPath in dune_pbm/main.php to the location of
dune_pbm_data.

The files in the dune_pbm_data and the files within need to be writable 
by the www-data group.

That's it. The game creates a new json file with all the needed game data
each turn, saving the old turn data as a file with with a time stamp.
