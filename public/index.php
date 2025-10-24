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
require_once __DIR__ . '/../app/controllers/PaymentController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';


// Create a reusable instance of OrderController
$orderController = new OrderController($pdo);

// Get the action from the query string (?action=...), default to 'home'
$action = $_GET['action'] ?? 'home';

// Routing based on the action parameter
switch ($action) {
    case 'dashboard':
        (new DashboardController($pdo))->index();
        break;

    case 'chart':
        (new DashboardController($pdo))->generateChart();
        break;


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
     case 'webauthn_login_options':
        (new AuthController($pdo))->webauthnLoginOptions();
        break;
    case 'webauthn_verify_assertion':
        (new AuthController($pdo))->webauthnVerifyAssertion();
        break;  
    case 'webauthn_register_options':
        (new AuthController($pdo))->webauthnRegisterOptions();
        break;
    case 'webauthn_finish_registration':
        (new AuthController($pdo))->webauthnFinishRegistration();
        break;
    case 'webauthn_setup':
        (new AuthController($pdo))->webauthnSetup();
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
        if (session_status() === PHP_SESSION_NONE) session_start();
        $currentUserType = $_SESSION['user']['usertype'] ?? null;
        // If the user is logged in as admin, show admin home
        if ($currentUserType === 'admin') {
            (new AdminHomeController($pdo))->index();
        } else {
            // Otherwise, show regular home page
            (new HomeController($pdo))->index();
        }
        break;
    
        // ...existing code...
        case 'payment_page':
            (new PaymentController($pdo))->paymentPage();
            break;
        case 'create_paypal_order':
            (new PaymentController($pdo))->createPayPalOrder();
            break;
        case 'capture_paypal_order':
            (new PaymentController($pdo))->capturePayPalOrder();
            break;
        
}
