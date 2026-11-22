<?php
require_once __DIR__ . '/AlkoProduct.php';

class Filter {
	public int $curPage = 0 { set => max(0, $value); }
	public int $pageSize = 25;

	public ?string $type = null;
	public ?string $bottleSize = null;
	public ?float $minPrice = null;
	public ?float $maxPrice = null;

	public function getPageOffset() {
		return $this->pageSize * $this->curPage;
	}

	public function passesFilter(AlkoProduct $product): bool {
		if ($this->type !== null && $this->type !== $product->type) return false;
		if ($this->bottleSize !== null && $this->bottleSize !== $product->bottleSize) return false;

		if ($this->minPrice !== null && $this->minPrice > $product->price) return false;
		if ($this->maxPrice !== null && $this->maxPrice < $product->price) return false;
		
		return true;
	}

}
