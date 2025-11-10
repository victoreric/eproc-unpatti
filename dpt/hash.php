<?php
$nama='victor eric';
$nip='198209292010121008';

 $hash1=MD5($nama);
 $hash2=MD5($nip);
echo $hash1.'_'.$hash2;
?>
