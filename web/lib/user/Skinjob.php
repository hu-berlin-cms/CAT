<?php

/*
 * ******************************************************************************
 * Copyright 2011-2017 DANTE Ltd. and GÉANT on behalf of the GN3, GN3+, GN4-1 
 * and GN4-2 consortia
 *
 * License: see the web/copyright.php file in the file structure
 * ******************************************************************************
 */

/**
 * This file contains a class for handling switching between skin frontends.
 *
 * @package Developer
 */
/**
 * 
 */

namespace web\lib\user;

use \Exception;

/**
 * This class handles user UI skin handling.
 * 
 * @author Stefan Winter <stefan.winter@restena.lu>
 */
class Skinjob {

    /**
     * The skin that was selected
     * 
     * @var string
     */
    public $skin;
    
    /**
     * Initialise the skin.
     * 
     * @param string $selectedSkin the name of the skin to use
     */
    public function __construct($selectedSkin = NULL) {
        // input may have been garbage. Sanity-check and fall back to default skin if needed
        $actualSkin = CONFIG['APPEARANCE']['skins'][0];
        if (in_array($selectedSkin, CONFIG['APPEARANCE']['skins'])) {
            $correctIndex = array_search($selectedSkin, CONFIG['APPEARANCE']['skins']);
            $actualSkin = CONFIG['APPEARANCE']['skins'][$correctIndex];
        }

        $this->skin = $actualSkin;
        $_SESSION['skin'] = $actualSkin;

    }

    /**
     * constructs a URL to the main resources directories. Searches for the file
     * first in the current skin's resource dir, then falls back to the global
     * resources dir, or returns FALSE if the requested file could not be found
     * at either location.
     * 
     * @param string $resourcetype which type of resource do we need a URL for?
     * @param string $filename the name of the file being searched.
     * @return string|boolean the URL to the resource, or FALSE if this file does not exist
     * @throws Exception if something went wrong during the URL construction
     */
    public function findResourceUrl($resourcetype, $filename, $submodule = '') {
        switch ($resourcetype) {
            case "CSS":
                $path = "/resources/css/";
                break;
            case "IMAGES":
                $path = "/resources/images/";
                break;
            case "BASE":
                $path = "/";
                break;
            case "EXTERNAL":
                $path = "/external/";
                break;
            default:
                throw new Exception("findResourceUrl: unknown type of resource requested");
        }

        // does the file exist in the current skin's directory? Has precedence
        if ($submodule !== '' && file_exists(__DIR__ . "/../../skins/" . $this->skin . "/" . $submodule . $path . $filename)) {
            $extrapath = "/skins/" . $this->skin . "/" . $submodule;
        }
        elseif (file_exists(__DIR__ . "/../../skins/" . $this->skin . $path . $filename)) {
            $extrapath = "/skins/" . $this->skin;
        } elseif (file_exists(__DIR__ . "/../../" . $path . $filename)) {
            $extrapath = "";
        } else {
            return FALSE;
        }       
        return htmlspecialchars(\core\CAT::getRootUrlPath() . $extrapath . $path . $filename, ENT_QUOTES);
    }

}
