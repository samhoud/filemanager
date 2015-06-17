<?php
namespace Samhoud\FileManager\Contracts;

interface FileSystemObject {
	public function isFile();
	public function isDirectory();
}