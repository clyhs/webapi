<?php


$str = "http://webapi.abigfish.org/static/upload/chat/2017-11-10/5a05634269543.mp4";

echo substr($str,strripos(str,"\."),3);