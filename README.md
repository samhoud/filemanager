# &samhoud FileManager

&samhoud FileManager is a simple filesystem wrapper for Laravel 5.1. Supports multiple local filesystems. 
It provides a basic filemanager and an image filemanager, for validation of uploaded images.


## Requirements

- Laravel > 5.0
- Intervention/Image (https://github.com/Intervention/image)


## Installation

Add the github repository and the package to your `composer.json` and run ```composer update``` :

```yaml
{
 	"repositories": [
        {
            "type": "git",
            "url":  "https://github.com/samhoud/filemanager"
        }
    ],
    "require": {
        "samhoud/filemanager": "dev-master"
    }
}
```
Add the service provider to ```config/app.php``` in your Laravel project:

```php
'providers' => [
// ...
Samhoud\FileManager\FileManagerServiceProvider::class,
];
```

## Configuration

FileManager will load your local filesystem by default for the basic filemanager and the image filemanager. Files are uploaded to the root of the filesystem by default. You can always change the filesystem or upload location after resolving the object from the IoC Container (see code examples).

### Change filesystems
If you want to provide a custom filesystem (as configured in ```config/filesystems.php```) run in your terminal:
```php artisan vendor:publish --provider="Samhoud\FileManager\FileManagerServiceProvider" --tag="config"```

You can now assign the desired filesystem in ```config/filemanager.php```

Change
```php
'filemanagerdisk'   => 'local',
```

to: 
```php
'filemanagerdisk'   => 'yourfilesystem',
```

### Change upload path
To change the default upload path, you can provide a path name or a date pattern. Change in ```config/filemanager.php```

Change
```php
'uploadlocation'    => ['path' => ''],
```

to: 
```php
'uploadlocation'    => ['path' => 'path/to/directory'],
```

or to:
```php 
'uploadlocation'    => ['date' => 'Y/m'],
```
This will upload your files for example to: ```filesystemroot/2015/06/``` 


## Code Examples

```php
// Get the FileManager from container.
$fileManager = app(Samhoud\FileManager\FileManager::class);

// Get the ImageManager from container.
$imageManager = app(Samhoud\FileManager\ImageManager::class);

// Change the filesystem
$disk = app('filesystem')->disk('myDisk');
$imageManager->setFileSystem($this->app['filesystem']->disk($disk));


// Change the upload location
 $settings = [
 	'uploadSettings' => ['path' => 'new/location']  
 	];
$imageManager->setSettings($settings);

// Get a list of files in the root directory and the subdirectories of the filesystem
$fileManager->listFiles();

// Get a list of files in a directory in a subdirectory (and subdirectories of this directory) of the filesystem
$fileManager->listFiles('logs/');

// Get a list of images in a directory (this works on the basic FileManager as well)
$fileManager->listImages();
$fileManager->listImages('uploads/');

// Check if directory exists
$fileManager->directoryExists('path/to/dir');

// Check if file exists
$fileManager->fileExists('path/to/file');

// Create directory
$fileManager->makeDirectory('path/to/new/dir');

// Delete file
$fileManager->deleteFile('path/to/file');

// Read contents of a file
$fileManager->read('path/to/file');


// Upload file
$file = Request::get('uploaded_file');
$fileManager->upload($file);

// Upload file to custom location
$arguments = [
	'uploadSettings' => ['path' => 'custom/location']  
]
$file = Request::get('uploaded_file');
$fileManager->upload($file, arguments);

// Upload image
$image = Request::get('uploaded_image');
$imageManager->upload($image);

//By default, non-image uploads are passed to the underlying basic FileManager. To prevent this, disable the upload of non-image files:
$nonImage = Request::get('uploaded_non_image');
$imageManager->uploadNonImages = false;
$imageManager->upload($nonImage); //false


// Controller example to display images in a directory
namespace App\Http\Controllers;

use Samhoud\FileManager\ImageManager;

class MediaController{
	protected  $fileManager;

	public function __construct(ImageManager $fileManager)
	{
		$this->fileManager = $fileManager;
	}

	public function showImages(){
		$images = $this->fileManager->listImages();
		return view('media.images')->with(['images' => $images]);
	}

}
*/

```

## Contributing

Contributions to the FileManager library are welcome. Please note the following guidelines before submiting your pull request.

- Follow [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
- Write tests for new functions and added features

## License

Intervention Image is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2015 [Bas van Vliet](http://samhoud.com/)
