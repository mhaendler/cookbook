<?php

namespace OCA\Cookbook\Controller\Implementation;

use OCA\Cookbook\Helper\RestParameterParser;
use OCA\Cookbook\Service\DbCacheService;
use OCA\Cookbook\Service\RecipeService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;

class ShoppingListImplementation {
	
	/** @var UserFolderHelper */
	private $userFolder;

	/** @var IRequest */
	private $request;

	public function __construct(
		IRequest $request,
		UserFolderHelper $userFolder,
	) {
		$this->request = $request;
	}

	/**
	 * List all available ShoppingList Entries.
	 *
	 * @return JSONResponse
	 */
	public function list() {
		die(__FUNCTION__);
	}

	/**
	 * Adds a new item to the Shopping list
	 * @return JSONResponse
	 */
	public function addItem() {
		die(__FUNCTION__);
	}

    /**
     * Checks a item from the shopping list
     */
    public function checkItem() {
		die(__FUNCTION__);
	}
}
