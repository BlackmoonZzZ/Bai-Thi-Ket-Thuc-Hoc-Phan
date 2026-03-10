<?php
/**
 * User Frontend Router
 * Routes to different pages based on ?page= parameter
 */
session_start();
require_once __DIR__ . '/../includes/auth.php';

// Get requested page
$page = $_GET['page'] ?? 'home';

// Pages that don't require login
$publicPages = ['home', 'product', 'search', 'login', 'register', 'forgot_password'];

// Pages that require login
$protectedPages = ['dashboard', 'cart', 'checkout', 'profile', 'wallet', 'library', 'transaction_history', 'orders'];

// Check if page exists and route accordingly
switch ($page) {
    case 'home':
        include __DIR__ . '/views/home.php';
        break;

    case 'product':
        include __DIR__ . '/views/product_detail.php';
        break;

    case 'search':
        if (file_exists(__DIR__ . '/views/search.php')) {
            include __DIR__ . '/views/search.php';
        } else {
            include __DIR__ . '/views/home.php';
        }
        break;

    case 'login':
        if (Auth::isLoggedIn()) {
            if (Auth::isAdmin()) {
                 header('Location: ../admin/index.php');
            } else {
                 header('Location: index.php?page=dashboard');
            }
            exit;
        }
        include __DIR__ . '/views/login.php';
        break;

    case 'register':
        if (Auth::isLoggedIn()) {
            if (Auth::isAdmin()) {
                 header('Location: ../admin/index.php');
            } else {
                 header('Location: index.php?page=dashboard');
            }
            exit;
        }
        include __DIR__ . '/views/register.php';
        break;

    case 'dashboard':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/dashboard/index.php';
        break;

    case 'cart':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/cart.php';
        break;

    case 'checkout':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/checkout.php';
        break;

    case 'profile':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/profile.php';
        break;

    case 'wallet':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/wallet.php';
        break;

    case 'library':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/library.php';
        break;

    case 'history':
    case 'transaction_history':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/transaction_history.php';
        break;

    case 'orders':
        if (!Auth::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        include __DIR__ . '/views/orders.php';
        break;

    case 'logout':
        Auth::logout();
        header('Location: index.php?page=home');
        exit;

    default:
        // Page not found - show home
        include __DIR__ . '/views/home.php';
        break;
}
