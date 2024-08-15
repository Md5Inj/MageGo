<?php

declare(strict_types=1);

$phar=new Phar('deploy.phar');
$phar->buildFromDirectory('files/');
if (Phar::canCompress(Phar::GZ))
{
    $phar->compressFiles(Phar::GZ);
}
else if (Phar::canCompress(Phar::BZ2))
{
    $phar->compressFiles(Phar::BZ2);
}
