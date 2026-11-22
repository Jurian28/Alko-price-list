<?php
require_once __DIR__ . '/CsvReader.php';
session_start();

function postString(string $key): ?string {
	if ($_POST[$key] == null) return null;

	return trim($_POST[$key]);
}

function postFloat(string $key): ?float {
	if ($_POST[$key] == null) return null;

	return (float) $_POST[$key];
}

$filter = $_SESSION["filter"] ?? new Filter();

$action = $_POST['action'] ?? null;

match ($action) {
	'prev'  => $filter->curPage-- ,
	'next'  => $filter->curPage++,
	default => null,
};

if ($action === 'filter') {
	$filter->type       = postString('type');
	$filter->bottleSize = postString('bottleSize');
	$filter->minPrice   = postFloat('minPrice');
	$filter->maxPrice   = postFloat('maxPrice');
	$filter->curPage    = 0;   // new filter, start from the first page
}

if ($action === 'clear') {
	$filter = new Filter();
}

$csvReader = new CsvReader();
$alkoProductList = $csvReader->importAlkoCsv("alko.csv", 4, $filter);

$_SESSION['filter'] = $filter
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= htmlspecialchars($alkoProductList->listName) ?></title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

	<form method="post"> <!-- filter -->
		<label for="type">Type</label>
		<input type="text" id="type" name="type"
			   value="<?= htmlspecialchars($filter->type ?? '') ?>">

		<label for="bottleSize">Bottle size</label>
		<input type="text" id="bottleSize" name="bottleSize"
			   value="<?= htmlspecialchars($filter->bottleSize ?? '') ?>">

		<label for="minPrice">Min price</label>
		<input type="number" id="minPrice" name="minPrice" min="0" step="0.01"
			   value="<?= htmlspecialchars((string) ($filter->minPrice ?? '')) ?>">

		<label for="maxPrice">Max price</label>
		<input type="number" id="maxPrice" name="maxPrice" min="0" step="0.01"
			   value="<?= htmlspecialchars((string) ($filter->maxPrice ?? '')) ?>">

		<button type="submit" name="action" value="filter">Filter</button>
		<button type="submit" name="action" value="clear">Clear</button>
	</form>

	
	<?php if (count($alkoProductList->getList()) == 0): ?>
		<p>No products found</p>
	<?php else: ?>
		<table>
			<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Manufacturer</th>
				<th>Bottle size</th>
				<th>Price</th>
				<th>Price per liter</th>
				<th>Type</th>
				<th>Country</th>
				<th>Vintage</th>
				<th>Alcohol %</th>
				<th>Energy kcal/100 ml</th>
			</tr>
			<?php foreach ($alkoProductList->getList() as $product): ?>
			<tr>
				<td><?= htmlspecialchars($product->number) ?></td>
				<td><?= htmlspecialchars($product->name) ?></td>
				<td><?= htmlspecialchars($product->manufacturer) ?></td>
				<td><?= htmlspecialchars($product->bottleSize) ?></td>
				<td><?= htmlspecialchars($product->price) ?></td>
				<td><?= htmlspecialchars($product->pricePerLiter) ?></td>
				<td><?= htmlspecialchars($product->type) ?></td>
				<td><?= htmlspecialchars($product->countryOfManufacture) ?></td>
				<td><?= htmlspecialchars($product->vintage) ?></td>
				<td><?= htmlspecialchars($product->alcoholPercentage) ?></td>
				<td><?= htmlspecialchars($product->energyKcalper100ml) ?></td>
			</tr>
			<?php endforeach; ?>

		</table>

		<form method="post"> <!-- pagination -->
			<button type="submit" name="action" value="prev">Prev</button>
			<button type="submit" name="action" value="next">Next</button>
		</form>

	<?php endif; ?>

</body>
</html>
