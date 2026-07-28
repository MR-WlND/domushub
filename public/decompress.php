<?php
$gzFile = __DIR__ . '/assets/vendor/tesseract/lang/eng.traineddata.gz';
$outFile = __DIR__ . '/assets/vendor/tesseract/lang/eng.traineddata';

if (!file_exists($gzFile)) {
    echo "GZ file does not exist!<br>";
    exit;
}

echo "Decompressing $gzFile to $outFile...<br>";

$gzData = file_get_contents($gzFile);
if ($gzData === false) {
    echo "Failed to read GZ file!<br>";
    exit;
}

$unzipped = gzdecode($gzData);
if ($unzipped === false) {
    echo "Failed to decompress GZ data!<br>";
    exit;
}

if (file_put_contents($outFile, $unzipped) !== false) {
    echo "SUCCESS: Created uncompressed eng.traineddata (" . strlen($unzipped) . " bytes)<br>";
    @unlink($gzFile); // Delete the gz file to save space
    echo "Deleted temporary GZ file.<br>";
} else {
    echo "FAILED to write uncompressed file!<br>";
}
