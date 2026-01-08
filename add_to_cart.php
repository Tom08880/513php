<?php
// [file name]: add_to_cart.php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $product_name = isset($_POST['product_name']) ? htmlspecialchars(trim($_POST['product_name'])) : 'Product';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';

    // Validate input
    if ($product_id <= 0 || $quantity <= 0 || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product information.']);
        exit();
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product already exists in cart
    $item_index = -1;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['product_id'] == $product_id) {
            $item_index = $index;
            break;
        }
    }

    // Add or update item in cart
    if ($item_index >= 0) {
        // Update existing item quantity
        $_SESSION['cart'][$item_index]['quantity'] += $quantity;
    } else {
        // Add new item
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'price' => $price,
            'image_url' => $image_url,
            'quantity' => $quantity
        ];
    }

    // Calculate total items
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart_count' => $total_items,
        'cart_total' => count($_SESSION['cart'])
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return cart info for AJAX requests
    $total_items = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'cart_count' => $total_items,
        'cart_total' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>