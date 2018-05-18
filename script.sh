#!/bin/bash

#mv ~/Downloads/screenshot.jpg /var/www/html/hqtest/raw.jpg
#convert raw.jpg -crop 400x494+43+209 image.jpg
#php ans.php


import -window "Screen Cast - Google Chrome" raw.png
convert raw.png -crop 440x511+710+350 image.png
php cheat.php
