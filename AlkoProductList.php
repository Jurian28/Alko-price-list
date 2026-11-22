<?php
require_once __DIR__ . '/AlkoProduct.php';

class AlkoProductList {
	public int $totalPages = 1;

	private array $alkoProducts = [];
	public string $listName = "";

	public function add(AlkoProduct $product) {
		$this->alkoProducts[] = $product;
	}
	public function addMany(array $products) {
		foreach ($products as $product) {
			$this->add($product);
		}
	}
	public function getList(): array {
		return $this->alkoProducts;
	}
}

