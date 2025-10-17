<?php
$w=400;$h=200;
$img=imagecreatetruecolor($w,$h);
imagesavealpha($img,true);

// Dégradé rose clair -> rose soutenu
$c1=[255,192,203]; $c2=[255,105,180];
for($y=0;$y<$h;$y++){
  $r=$c1[0]+($c2[0]-$c1[0])*$y/$h;
  $g=$c1[1]+($c2[1]-$c1[1])*$y/$h;
  $b=$c1[2]+($c2[2]-$c1[2])*$y/$h;
  $col=imagecolorallocate($img,$r,$g,$b);
  imageline($img,0,$y,$w,$y,$col);
}

// Couleurs
$white=imagecolorallocate($img,255,255,255);
$pink=imagecolorallocate($img,255,20,147);

// Police (Windows)
$font="C:/Windows/Fonts/arial.ttf"; 

// Textes
$name="ShipiShop";
$slogan="La douceur du shopping au féminin";
$sz1=28;$sz2=14;

// Mesure boîtes
$bb1=imagettfbbox($sz1,0,$font,$name);
$bb2=imagettfbbox($sz2,0,$font,$slogan);
$w1=$bb1[2]-$bb1[0];$h1=$bb1[1]-$bb1[7];
$w2=$bb2[2]-$bb2[0];$h2=$bb2[1]-$bb2[7];
$tot=$h1+10+$h2;
$y1=($h-$tot)/2+$h1; $y2=$y1+10+$h2;
$x1=($w-$w1)/2; $x2=($w-$w2)/2;

// Texte centré
imagettftext($img,$sz1,0,$x1,$y1,$white,$font,$name);
imagettftext($img,$sz2,0,$x2,$y2,$white,$font,$slogan);

// Petit cœur bien positionné (bas à droite, marge)
$cx=$w-35; $cy=$h-35; // plus proche du centre du bas droit
$r=15;
$points=[];
for($i=0;$i<360;$i+=5){
    $a=deg2rad($i);
    $x=$cx+$r*16*pow(sin($a),3)/17;
    $y=$cy-$r*(13*cos($a)-5*cos(2*$a)-2*cos(3*$a)-cos(4*$a))/17;
    $points[]=$x; $points[]=$y;
}
imagefilledpolygon($img,$points,count($points)/2,$pink);

// Sauvegarde + affichage
imagepng($img,__DIR__."/logo_shipishop.png");
header("Content-Type:image/png");
imagepng($img);
imagedestroy($img);
