<?php

require ('EpubExtractor.php');

$myEpub='ebook.epub'; 
EpubExtractor::setFileName($myEpub); //first set the filename
$infos= EpubExtractor::extractInfo(); //then get all informations

echo $infos['title'].'<br>';
echo $infos['language'].'<br>';
echo $infos['creator'].'<br>';
echo $infos['identifier'].'<br>';
echo $infos['publisher'].'<br>';
echo $infos['date'].'<br>';
echo $infos['description'].'<br>';
   
displayImage($infos['coverData']);
   
   
function displayImage($image) {
    $imgData = base64_encode($image);
    $src = 'data: '.'image/jpeg'.';base64,'.$imgData;
    echo '<img width=200px src="'.$src.'">';
  }
