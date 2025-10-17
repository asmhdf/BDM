<?php
// Start the session to manage user login state
session_start();

// Include configuration (database connection) and controllers
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/AdminHomeController.php';

// Create a reusable instance of OrderController
$orderController = new OrderController($pdo);

// Get the action from the query string (?action=...), default to 'home'
$action = $_GET['action'] ?? 'home';

// Routing based on the action parameter
switch ($action) {

    // ----------- Admin Product Actions -----------
    case 'admin_delete_image':
        (new ProductController($pdo))->adminDeleteImage();
        break;
    case 'admin_products':
        (new ProductController($pdo))->adminList();
        break;
    case 'admin_add_product':
        (new ProductController($pdo))->adminAddProduct();
        break;
    case 'admin_edit_product':
        (new ProductController($pdo))->adminEditProduct();
        break;
    case 'admin_delete_product':
        (new ProductController($pdo))->adminDeleteProduct();
        break;

    // ----------- Admin Order Actions -----------
    case 'admin_list_orders':
        $orderController->adminListOrders();
        break;
    case 'admin_view_order':
        $orderController->adminViewOrder();
        break;
    case 'admin_update_order_status':
        $orderController->adminUpdateOrderStatus();
        break;

    // ----------- Authentication Actions -----------
    case 'logout':
        (new AuthController($pdo))->logout();
        break;
    case 'register':
        (new AuthController($pdo))->register();
        break;
    case 'login':
        (new AuthController($pdo))->login();
        break;

    // ----------- Order Actions -----------
    case 'order_form':
        (new OrderController($pdo))->form();
        break;
    case 'order_submit':
        (new OrderController($pdo))->submit();
        break;
    case 'order_confirm':
        (new OrderController($pdo))->confirm();
        break;
    case 'order_pdf':
        (new OrderController($pdo))->pdf();
        break;

    // ----------- Product Actions -----------
    case 'product':
        (new ProductController($pdo))->show();
        break;

    // ----------- Cart Actions -----------
    case 'add_to_cart':
        (new CartController($pdo))->add();
        break;
    case 'cart':
        (new CartController($pdo))->show();
        break;
    case 'update_cart':
        (new CartController($pdo))->update();
        break;
    case 'remove_from_cart':
        (new CartController($pdo))->remove();
        break;

    // ----------- Default: Home Page -----------
    case 'home':
    default:
        // If the user is logged in as admin, show admin home
        if (!empty($_SESSION['user']) && $_SESSION['user']['usertype'] === 'admin') {
            (new AdminHomeController($pdo))->index();
        } else {
            // Otherwise, show regular home page
            (new HomeController($pdo))->index();
        }
        break;
}
