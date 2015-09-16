<?php

return [
/**
*
* Provide disk name for the file manager
* You can configure a disk in config/filesystems of your laravel project
* Only local disks supported
*
* Default: local
*/
'filemanagerdisk'   => 'local',

/**
*
* Provide disk name for the image manager
* You can configure a disk in config/filesystems of your laravel project
* Only local disks supported
*
* Default: local
*/
'imagemanagerdisk'  => 'local',

/**
*
* Provide the default upload location.
* Don't start with a '/'.
* options:
*  'path' : the path, relative to the disk location. Example:
*      'uploadlocation'    => ['path' => 'uploads/'];
*  'date' : upload by date format. Example:
*      'uploadlocation'    => ['date' => 'Y/m'];
*
* default : ['path' => '']
*/
'uploadlocation'    => ['path' => ''],


/**
 * Templates:
 * Provide image filter templates
 * Provide desired pixels for width and height
 * options:
 * 'TEMPLATENAME' => [
 *      width:              int (default: null)
 *      height:             int (default: null)
 *      keepAspectRatio:    bool (default: true)
 *      preventUpsizing:    bool (default: false)
 * ]
 */
'templates' => [
    'default' => [
        'width'             => 600,
        'height'            => 400,
        'keepAspectRatio'   => true,
        'preventUpsizing'   => true,
    ],
],


];