<?php
require_once __DIR__ . '/AlkoProduct.php';
require_once __DIR__ . '/AlkoProductList.php';
require_once __DIR__ . '/Filter.php';

class CsvReader {

	public function importAlkoCsv(string $filepath, int $nonDataLines, Filter $filter): AlkoProductList {
		$alkoProductList = new AlkoProductList();

		$handle = fopen($filepath, "r");
		if ($handle === false) {
			return $alkoProductList;
		}

		$alkoProductList->listName = $this->getListName($handle);
		$this->skipLines($handle, $nonDataLines - 1);

		$matches = $this->getMatchingProducts($handle, $filter);
		fclose($handle);

		// Clamp the page so it can never go past the last one
		$totalPages = max(1, (int) ceil(count($matches) / $filter->pageSize));
		$filter->curPage = min($filter->curPage, $totalPages - 1);

		$alkoProductList->totalPages = $totalPages;
		$alkoProductList->addMany(
			array_slice($matches, $filter->getPageOffset(), $filter->pageSize)
		);

		return $alkoProductList;
	}

	private function getMatchingProducts($handle, Filter $filter): array {
		$matches = [];

		while (($row = fgetcsv($handle)) !== false) {
			if (count($row) < 28) continue;

			$product = $this->getAlkoProductFromCsvRow($row);

			if ($filter->passesFilter($product)) {
				$matches[] = $product;
			}
		}

		return $matches;
	}


	private function getListName($handle): string {
		$row = fgetcsv($handle);

		return $row[0] ?? "";
	}

	private function getPageOfProducts($handle, Filter $filter): array {
		$alkoProductList = [];

		$paginateCounter = 0;
		while (($row = fgetcsv($handle)) !== false && $paginateCounter < $filter->pageSize) {
			$product = $this->getAlkoProductFromCsvRow($row);

			if ($filter->passesFilter($product)) {
				$alkoProductList[] = $product;
				$paginateCounter++;
			}
		}
		return $alkoProductList;
	}

	private function getAlkoProductFromCsvRow(array $row): AlkoProduct {
		$product = new AlkoProduct();

		$product->number = (int) $row[0];
		$product->name = $row[1];
		$product->manufacturer = $row[2];
		$product->bottleSize = $row[3];
		$product->price = (float) $row[4];
		$product->pricePerLiter = (float) $row[5];
		$product->type = $row[8];
		$product->countryOfManufacture = $row[12];
		$product->vintage = (int) $row[14];
		$product->alcoholPercentage = (float) $row[21];
		$product->energyKcalper100ml = (float) $row[27];

		return $product;
	}

	private function skipLines($handle, int $count): void {
		for ($i = 0; $i < $count; $i++) {
			if (fgetcsv($handle) === false) break;
		}
	}
	private function countLines(string $filepath, Filter $filter): int {
		$lines = 0;
		if ($handle = fopen($filepath, "r")) {
			while (($row = fgetcsv($handle)) !== false) {

				$product = $this->getAlkoProductFromCsvRow($row);
				if ($filter->passesFilter($product)) {
					$lines++;
				}
				$lines++;
			}
		}
		fclose($handle);

		return $lines;
	}
}
