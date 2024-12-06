<?php

namespace OCA\Cookbook\Controller;

use OCA\Cookbook\Controller\Implementation\ShoppingListImplementation;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ShoppingListController extends Controller {
	/** @var ShoppingListImplementation */
	private $impl;

	public function __construct(
		string $AppName,
		IRequest $request,
		ShoppingListImplementation $shoppingListImplementation,
	) {
		parent::__construct($AppName, $request);

		$this->impl = $shoppingListImplementation;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return JSONResponse
	 */
	public function list() {
		return $this->impl->list();
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function addItem() {
		return $this->impl->addItem();
	}

    /**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function checkItem() {
		return $this->impl->checkItem();
	}
}
