<?php
namespace Samhoud\FileManager\Contracts;

interface Directory {
	public function addItem(FileSystemObject $item);
	public function hasDirectories();
	public function hasFiles();
}