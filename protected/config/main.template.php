<?php

define("RIS_DATA_DIR", "/data/ris3-data/");
define("RIS_OMNIPAGE_DIR", "/data/nuance/");
define("PATH_IDENTIFY", "/usr/bin/identify");
define("PATH_CONVERT", "/usr/bin/convert");
define("PATH_TESSERACT", "/usr/local/bin/tesseract");
define("PATH_JAVA", "/usr/local/java/bin/java");
define("PATH_PDFTOTEXT", "/usr/bin/pdftotext");
define("PATH_PDFBOX", RIS_DATA_DIR . "pdfbox-app-1.8.5.jar");
define("PATH_PDFINFO", "/usr/bin/pdfinfo");
define("PATH_PDFTOHTML", "/usr/bin/pdftohtml");

define("PATH_PDF", RIS_DATA_DIR . "data/pdf/");
define("TMP_PATH", "/tmp/");
define("LOG_PATH", RIS_DATA_DIR . "logs/");
define("RU_PDF_PATH", RIS_DATA_DIR . "data/ru-pdf/");
define("OMNIPAGE_PDF_DIR", RIS_OMNIPAGE_DIR . "ocr-todo/");
define("OMNIPAGE_DST_DIR", RIS_OMNIPAGE_DIR . "ocr-dst/");
define("OMNIPAGE_IMPORTED_DIR", RIS_OMNIPAGE_DIR . "ocr-imported/");
define("TILE_CACHE_DIR", RIS_DATA_DIR . "tile-cache/tiles/");


define("RATSINFORMANT_BASE_URL", "https://www.ratsinformant.de");

ini_set("memory_limit", "256M");

define("SEED_KEY", "RANDOMKEY");

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
ini_set('mbstring.substitute_character', "none");
setlocale(LC_TIME, "de_DE.UTF-8");

require_once(__DIR__ . "/urls.php");


function ris_intern_address2geo($land, $plz, $ort, $strasse)
{
	return array("lon" => 0, "lat" => 0);
}

/**
 * @param Antrag $referenz
 * @param Antrag $antrag
 * @return bool
 */
function ris_intern_antrag_ist_relevant_mlt($referenz, $antrag) {
	return true;
}


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name'       => 'Ratsinformant',

	// preloading 'log' component
	'preload'    => array('log'),

	// autoloading model and component classes
	'import'     => array(
		'application.models.*',
		'application.components.*',
		'application.RISParser.*',
	),

	'modules'    => array(
		// uncomment the following to enable the Gii tool
		'gii' => array(
			'class'     => 'system.gii.GiiModule',
			'password'  => 'RANDOMKEY',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters' => array('*', '::1'),
		),
	),

	// application components
	'components' => array(
		'cache'        => array(
			'class' => 'system.caching.CFileCache',
		),
		'urlManager'   => array(
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => $GLOBALS["RIS_URL_RULES"],
		),
		'db'           => array(
			'connectionString'      => 'mysql:host=127.0.0.1;dbname=DB',
			'emulatePrepare'        => true,
			'username'              => 'ris',
			'password'              => 'PASSWORD',
			'charset'               => 'utf8',
			'queryCacheID'          => 'apcCache',
			'schemaCachingDuration' => 3600,
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => 'index/error',
		),
		'log'          => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning',
				),
				/*
				array(
					'class' => 'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'     => array(
		// this is used in contact page
		'adminEmail'     => 'info@ratsinformant.de',
		'adminEmailName' => "Ratsinformant",
		'skobblerKey'    => 'KEY',
		'baseURL'        => RATSINFORMANT_BASE_URL,
		'debug_log'      => true,
	),
);