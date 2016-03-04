<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<script src="js/js.cookie.js"></script>
	<script src="js/md5.js"></script>

</head>
<body>
<a href='index.php'><h1><i class="fa fa-home"></i>Tutorial-Portal</h1></a>
<?php

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}


function getFileCount($path) {
    $size = 0;
    $ignore = array('.','..','cgi-bin','.DS_Store');
    $files = scandir($path);
    foreach($files as $t) {
        if(in_array($t, $ignore)) continue;
        if (is_dir(rtrim($path, '/') . '/' . $t)) {
            $size += getFileCount(rtrim($path, '/') . '/' . $t);
        } else if(endsWith($t, ".mp4")){
            $size++;
        }
    }
    return $size;
}

function getSubFolderCount($path) {
    $size = 0;
    $ignore = array('.','..','cgi-bin','.DS_Store');
    $files = scandir($path);
    foreach($files as $t) {
        if(in_array($t, $ignore)) continue;
        if (is_dir(rtrim($path, '/') . '/' . $t)) {
            $size ++;
        }
    }
    return $size;
}


//http://stackoverflow.com/a/478161/1031984
function getDirSizeHuman($dir) {
    $f = "/var/customers/webs/rbsWeb/tuts/".$dir;
    //echo '/usr/bin/du -sk "' . $f .'"';
    $io = popen ( '/usr/bin/du -sk "' . $f .'"', 'r' );
    $size = fgets ( $io, 4096);
    $size = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
    if ($size > 1024*1024)
      return round($size / 1024 / 1024, 1)."GB";
    else
      return round($size / 1024, 0)."MB";
}

function getDirSize($dir) {
    $f = "/var/customers/webs/rbsWeb/tuts/".$dir;
    //echo '/usr/bin/du -sk "' . $f .'"';
    $io = popen ( '/usr/bin/du -sk "' . $f .'"', 'r' );
    $size = fgets ( $io, 4096);
    $size = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
    return $size;
}


if(!isset($_GET["tut"])) {
	echo "<h2 id='title'>&Uuml;bersicht</h2>";
	echo "<div id='courses'>";
	$cat_dirs = glob('vids/*', GLOB_ONLYDIR);
	foreach($cat_dirs as $cat){
    $tut_count = getSubFolderCount("$cat");
    if($tut_count == 0) continue;
    $cat = str_replace("vids/", "", $cat);
		echo "<h3>$cat<div class='tut_count'>$tut_count</div></h3><div class='categorie'>";
		$dirs = glob('vids/'.$cat.'/*', GLOB_ONLYDIR);
		foreach($dirs as $tut){
			$tut = str_replace("vids/$cat/", "", $tut);
			$cookieName = 'tut_'.md5($tut);
			$vidCount = getFileCount("vids/$cat/$tut/");
			if(!isset($_COOKIE[$cookieName])) {
				$currVid = 0;
			} else {
				$currVid = $_COOKIE[$cookieName] + 1;
			}

			echo "<a class='tut' href='index.php?cat=".rawurlencode($cat)."&tut=".rawurlencode($tut)."'><div class='tut_div'>$tut
			<div class='tut_progress' curr='$currVid' count='$vidCount'><div class='label'>$currVid/$vidCount</div></div></div></a>\n";
		}
		echo "</div>";
	}
	echo "</div>";
} else if(!isset($_GET["section"])){
	$tut = rawurldecode($_GET["tut"]);
	$cat = rawurldecode($_GET["cat"]);
  $size = getDirSizeHuman("vids/$cat/$tut/");
  $time = round(0.00005 * getDirSize("vids/$cat/$tut/") / 60, 1);
	echo "<h2 id='title'>$tut</h2>
        <a href='download.php?cat=".rawurlencode($cat)."&tut=".rawurlencode($tut)."'>
          <div class='downloadAll'><i class='fa fa-download'></i>Download Gesamter Kurs (".$size.")
            <div class='downloadHint'>Generierung der zip-Datei dauert etwa $time min!</div>
          </div></a>";
	$dirs = glob("vids/$cat/$tut/*", GLOB_ONLYDIR);
  echo "<div id='course'>";
  foreach($dirs as $sectionFolder){
    $section = str_replace("vids/$cat/$tut/", "", $sectionFolder);
		echo "<h3>$section
    <div class='sectionDownload'>
      <div class='downloadSize'>".getDirSizeHuman($sectionFolder)."</div>
      <a href='download.php?cat=".rawurlencode($cat)."&tut=".rawurlencode($tut)."&section=".rawurlencode($section)."'>
        <i class='fa fa-download'></i>
      </a>
    </div></h3>
    <div class='section'>";
		$files = glob("vids/$cat/$tut/$section/*.*");
		foreach($files as $entry){
			$name = str_replace("vids/$cat/$tut/$section/", "", $entry);
			if(endsWith($name, ".mp4")) {
				$name = str_replace(".mp4", "", $name);
				echo "<div file='$entry' class='video'><i class='fa fa-video-camera'></i>$name</div>\n";
			} else {
				echo "<div class='download'><i class='fa fa-download'></i><a href='$entry' download>$name</a></div>";
			}
		}
		echo "</div>";
	}
	echo "</div>";
	echo "<div id='video'>
			<video id='videoArea' controls='controls'>
				<source src='http://clips.vorwaerts-gmbh.de/big_buck_bunny.ogv' id='video_src'>
			</video>
		</div>
		<div id='progressbar'><div class='label'>N&auml;chstes Video in 10s...</div></div>
			";
} else {
	$section = $_GET["section"];


}
?>
</body>
</html>
