<?php
/**
 * Main application entry point
 * 
 * If a 'page' is provided, we route it to user/index.php.
 * By default, go to the storefront homepage.
 */
// Pass all routing to the user module index logic
require_once __DIR__ . '/user/index.php';
