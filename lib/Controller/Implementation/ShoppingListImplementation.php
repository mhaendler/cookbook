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

	/** @var RestParameterParser */
	private $restParser;

	public function __construct(
		IRequest $request,
		ShoppingListService $shoppingListService,
		RestParameterParser $restParameterParser,
	) {
		$this->request = $request;
		$this->shoppingListService = $shoppingListService;
		$this->restParser = $restParameterParser;
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
		$data = $this->restParser->getParameters();
		$newItem = "- [ ] " . $data['item'];
		$shoppingListItems = $this->shoppingListService->getShoppingListItems();
		$shoppingListItems[] = $newItem;
		$this->shoppingListService->saveShoppingListItems($shoppingListItems);
		return new JSONResponse($shoppingListItems, Http::STATUS_OK);
	}

    /**
     * Checks a item from the shopping list
     */
    public function checkItem() {
		// Update an item (check/uncheck)
		$data = $this->restParser->getParameters();
	
		$itemToUpdate = $data['item'];
		$checked = $data['checked'];
	
		$shoppingListItems = $this->shoppingListService->getShoppingListItems();
		$newList = [];
		foreach ($shoppingListItems as $item) {
			if (strpos($item, $itemToUpdate) !== false) {
				$item = $checked ? str_replace('[ ]', '[x]', $item) : str_replace('[x]', '[ ]', $item);
			}
			$newList[] = $item;
		}
		$this->shoppingListService->saveShoppingListItems($newList);
		return new JSONResponse($newList, Http::STATUS_OK);
	}

	/**
     * Deletes a item from the shopping list
     */
    public function deleteItem() {
		// Update an item (check/uncheck)
		$data = $this->restParser->getParameters();
	
		$itemToDelete = $data['item'];

		$shoppingListItems = $this->shoppingListService->getShoppingListItems();

		$newList = [];
		foreach ($shoppingListItems as $item) {
			if (strpos($item, $itemToDelete) === false) {
				$newList[] = $item;
			}
		}
		$this->shoppingListService->saveShoppingListItems($newList);
		return new JSONResponse($newList, Http::STATUS_OK);
	}
}
