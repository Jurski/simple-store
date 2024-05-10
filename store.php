<?php

$content = file_get_contents("products.json");
if ($content === false) {
    echo "Unable to read products.json";
    exit;
}

$products = json_decode($content);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Unable to parse products.json: " . json_last_error_msg();
    exit;
}

$shoppingCart = [];

function displayCart(array $cart): void
{
    echo "Your cart" . PHP_EOL . "---------------------------" . PHP_EOL;
    $total = 0;
    foreach ($cart as $product) {
        echo "$product->name:$product->quantity pcs (Per piece: $product->price) with sum of EUR $product->sum" . PHP_EOL;
        $total += $product->sum;
    }
    echo "Total: $total" . PHP_EOL;
}

echo "Products list" . PHP_EOL . "---------------------------" . PHP_EOL;

foreach ($products as $product) {
    $price = number_format($product->price, 2);
    echo "$product->id. $product->name: $price EUR" . PHP_EOL;
}

$customerShopping = true;

while ($customerShopping) {
    $productToBuy = (int)readline("Enter a product you want to buy (single product from 1-7)\n");

    $productIDs = array_column($products, "id");
    if (!in_array($productToBuy, $productIDs)) {
        echo "Please enter valid product ID" . PHP_EOL;
        continue;
    }

    $quantityToBuy = (int)readline("Enter quantity to buy (number value)\n");

    if ($quantityToBuy < 1) {
        echo "Please enter valid quantity" . PHP_EOL;
        continue;
    }

    if (!isset($shoppingCart[$productToBuy])) {
        $product = new stdClass();
        $product->id = $productToBuy;
        $product->name = $products[$productToBuy - 1]->name;
        $product->price = $products[$productToBuy - 1]->price;
        $product->quantity = $quantityToBuy;
        $product->sum = round($products[$productToBuy - 1]->price * $quantityToBuy, 2);
        $shoppingCart[$productToBuy] = $product;
    } else {
        $shoppingCart[$productToBuy]->quantity += $quantityToBuy;
        $shoppingCart[$productToBuy]->sum += round($shoppingCart[$productToBuy]->price * $quantityToBuy, 2);
    }

    displayCart($shoppingCart);

    $readyToPurchase = strtolower(readline("Ready to purchase? Enter y if yes"));

    if ($readyToPurchase === "y") {
        $shoppingCart = array();
        echo "Thank you for your purchase!";
        $customerShopping = false;
    } else continue;
}