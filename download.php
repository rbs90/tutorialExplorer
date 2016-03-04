<?php
    echo set_time_limit(0);
    $tut = rawurldecode($_GET["tut"]);
    $cat = rawurldecode($_GET["cat"]);

    $files = glob('tmp/*'); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }

    if(isset($_GET["section"])){
      $section = rawurldecode($_GET["section"]);
      $downloadName = $tut."_".$section;
      $folder = "vids/$cat/$tut/$section";
    } else {
      $downloadName = $tut;
      $folder = "vids/$cat/$tut";
    }

    $downloadName = str_replace(" ", "_", $downloadName);
    $downloadName = str_replace(".", "", $downloadName);
    echo $downloadName;

    $zipname = "tmp/$downloadName.zip";
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($folder) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='$zipname'");
    header('Content-Length: ' . filesize($zipname));
    header("Location: $zipname");

    ignore_user_abort(true);
    if (connection_aborted()) {
        unlink("$zipname");
    }
?>
