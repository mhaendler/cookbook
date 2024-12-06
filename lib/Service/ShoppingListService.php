<?php

namespace OCA\Cookbook\Service;

use Exception;
use OCA\Cookbook\Db\RecipeDb;
use OCA\Cookbook\Exception\HtmlParsingException;
use OCA\Cookbook\Exception\ImportException;
use OCA\Cookbook\Exception\NoRecipeNameGivenException;
use OCA\Cookbook\Exception\RecipeExistsException;
use OCA\Cookbook\Exception\UserFolderNotWritableException;
use OCA\Cookbook\Helper\DownloadHelper;
use OCA\Cookbook\Helper\FileSystem\RecipeNameHelper;
use OCA\Cookbook\Helper\Filter\JSON\JSONFilter;
use OCA\Cookbook\Helper\ImageService\ImageSize;
use OCA\Cookbook\Helper\UserConfigHelper;
use OCA\Cookbook\Helper\UserFolderHelper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IL10N;
use OCP\Image;
use OCP\PreConditionNotMetException;
use Psr\Log\LoggerInterface;

/**
 * Main service class for the shopping list extension
 *
 * @package OCA\Cookbook\Service
 */
class ShoppingListService {

    const string SHOPPING_LIST_NAME = 'shopping-list.md';

	private $root;
	private $user_id;
	/**
	 * @var UserFolderHelper
	 */
	private $userFolder;
	private $logger;

	public function __construct(
		?string $UserId,
		IRootFolder $root,
		UserFolderHelper $userFolder,
		LoggerInterface $logger,
	) {
		$this->user_id = $UserId;
		$this->root = $root;
		$this->userFolder = $userFolder;
		$this->logger = $logger;
	}

    public function checkIfFileExists(){
        $user_folder = $this->userFolder->getFolder();
        return $user_folder->nodeExists(self::SHOPPING_LIST_NAME);
    }

    public function createShoppingListFile(){
        $user_folder = $this->userFolder->getFolder();
        $user_folder->newFile(self::SHOPPING_LIST_NAME);
    }

    public function getShoppingListFile(){
        $user_folder = $this->userFolder->getFolder();
    
        foreach ($user_folder->getDirectoryListing() as $node) {    
			if ($this->isShoppingListFile($node)) {
				return $node;
			}
		}
        return null;
    }

    public function getShoppingListItems(){
        $file = $this->getShoppingListFile();
        if(null === $file){
            return '[]';
        }
        return explode("\n", trim($file->GetContent()));
    }

	public function saveShoppingListItems($items){
		$file = $this->getShoppingListFile();
        if(null === $file){
            return false;
        }
		$file->putContent(implode(PHP_EOL, $items).PHP_EOL);
		return true;
	}

	/**
	 * Get recipe file contents as an array
	 *
	 * @param File $file
	 */
	public function parseShoppingListFile($file): ?array {
		if (!$file) {
			return null;
		}

		return json_encode($file->getContent(), true);
	}

	/**
	 * Test if file is a shopping-list file
	 *
	 * @param File $file
	 *
	 * @return bool
	 */
	private function isShoppingListFile($file) {
		$allowedExtensions = ['md'];

		if ($file->getType() !== 'file') {
			return false;
		}

		$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
		$iext = strtolower($ext);

		if (!in_array($iext, $allowedExtensions)) {
			return false;
		}

		return true;
	}
}
