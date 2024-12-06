<?php

namespace OCA\Cookbook\Controller\Implementation;

use OCA\Cookbook\Helper\RestParameterParser;
use OCA\Cookbook\Service\DbCacheService;
use OCA\Cookbook\Service\RecipeService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\Cookbook\Service\ShoppingListService;

class ShoppingListImplementation {

	
	
	/** @var ShoppingListService */
	private $shoppingListService;

	/** @var IRequest */
	private $request;

	public function __construct(
		IRequest $request,
		ShoppingListService $shoppingListService,
	) {
		$this->request = $request;
		$this->shoppingListService = $shoppingListService;
	}

	/**
	 * List all available ShoppingList Entries.
	 *
	 * @return JSONResponse
	 */
	public function list() {
		if(false === $this->shoppingListService->checkIfFileExists()){
			$this->shoppingListService->createShoppingListFile();
		}
		
		$shoppingListItems = $this->shoppingListService->getShoppingListItems();
		return new JSONResponse($shoppingListItems, Http::STATUS_OK);
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
