<?php
session_start(); // Start the session for all routes

include __DIR__ . '/includes/db_connect.php';

// route.php

// Get the requested URI
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Define your routes
// Each route maps a URI pattern to a corresponding PHP file to include
$routes = [
    '' => 'views/members.php', // Default route for the homepage when URI is empty after base path removal
    'project1' => 'views/members.php', // Route for direct access to /project1/
    'views/add_member.php' => 'views/add_member.php', // Route for adding a member
    'views/edit_member.php' => 'views/edit_member.php', // Route for editing a member
    'views/delete_member.php' => 'views/delete_member.php', // Route for deleting a member
    'views/activities/activity_management.php' => 'views/activities/activity_management.php', // Route for activity management
    'views/activities/add_activity.php' => 'views/activities/add_activity.php', // Route for adding an activity
    'views/activities/edit_activity.php' => 'views/activities/edit_activity.php', // Route for editing an activity
    'views/activities/delete_activity.php' => 'views/activities/delete_activity.php', // Route for deleting an activity
    'views/activities/manage_participation.php' => 'views/activities/manage_participation.php', // Route for managing participation
];

// Remove the project subdirectory from the request URI if present
// This assumes your project is in a subdirectory like /project1/
// If your project is at the root of the domain, this can be simplified.
$base_path = '/project1';
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
    $request_uri = trim($request_uri, '/');
}

// If the request URI is empty, it means we are at the root of the project
if (empty($request_uri)) {
    $request_uri = '';
}

// Find the corresponding file in our routes
$target_file = null;
foreach ($routes as $route_pattern => $file) {
    // Simple direct match for now. More complex routing (regex) can be added later.
    if ($request_uri === trim($route_pattern, '/')) {
        $target_file = $file;
        break;
    }
}

// If a route is found, include the file
if ($target_file && file_exists($target_file)) {
    include $target_file;
} else {
    // Handle 404 Not Found
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>The page you requested could not be found.</p>";
}
