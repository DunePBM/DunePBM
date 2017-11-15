# DunePBM
A Dune Boardgame PBM in PHP.

This is designed to run a Play by Forum game of the Dune Boardgame,
using the WBC 2016 rules set.

Basically, Dune is a great game with the slight problem of needing six
people to play it. People routinely play games over forums where the
cards and other hidden information is handled by a GM. This program
aims to take care of the needing a GM, so then everyone gets to play
and local, if not global, happiness is increased.

Currently, the program will set up a game, assigning traitors, treachery 
cards, and starting token locations. 

My plan is to get the game running, then to add features to the forum
and mail sections.


## Setup Instructions
Originally I was testing this on an Orange Pi with Apache2 and PHP 5.6.

You can have the dune_pbm and dune_pbm_data folders wherever you want, as 
long you set the $gameDir /var/www/html/index.php to the location of 
.dune_pbm in and set $dataDir in dune_pbm/main.php to the location of
.dune_pbm_data.

But now I have been running on a free web host with php support, but worse
file permissions access. So now I keep everything in the html folder, 
hiding .dune_pbm and .dune_pbm_data. Then using .htaccess to block access
to the game folder.

The files in the dune_pbm_data and the files within need to be writable 
by the www-data group.

That's it. The game creates a new json file with all the needed game data
each turn, saving the old turn data as a file with with a time stamp.