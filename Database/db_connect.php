<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "Zealous_CSRP";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// makes the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
if ($conn->query($sql) === TRUE) {
} 
else {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// makes the users table if it doesn't exist
$tableSQL = "
CREATE TABLE IF NOT EXISTS users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('User Admin','CSR Rep','PIN','Platform Manager') DEFAULT 'PIN',
    status ENUM('Active','Inactive') DEFAULT 'Active'
);";

if (!$conn->query($tableSQL)) {
    die('Error creating table: ' . $conn->error);
}

// counts the number of entries in the table
$check = $conn->query("SELECT COUNT(*) AS total FROM users");
$count = $check->fetch_assoc()['total'];

// if the number counted is 0, it will create 100 sample user data
if ($count == 0) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $roles = ['User Admin', 'CSR Rep', 'PIN', 'Platform Manager'];
    $statuses = ['Active', 'Inactive'];

    for ($i = 1; $i <= 100; $i++) {
        $name = "user{$i}";
        $email = "email{$i}@example.com";
        $password = "password{$i}";
        $role = $roles[array_rand($roles)];
        $status = $statuses[array_rand($statuses)];
        $stmt->bind_param("sssss", $name, $email, $password, $role, $status);
        $stmt->execute();
    }
}

// make the user profile table if it doesn't exist
$profileTableSQL = "
CREATE TABLE IF NOT EXISTS profiles (
    profileID INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(50) UNIQUE NOT NULL,
    permissions TEXT,
    description TEXT,
    status ENUM('Active','Inactive') DEFAULT 'Active'
);";

if (!$conn->query($profileTableSQL)) {
    die('Error creating profiles table: ' . $conn->error);
}

// insert the 4 default user profiles into profile table if count is 0
$checkProfile = $conn->query("SELECT COUNT(*) AS total FROM profiles");
$profileCount = $checkProfile->fetch_assoc()['total'];

if ($profileCount == 0) {
    $stmt = $conn->prepare("INSERT INTO profiles (role, permissions, description) VALUES (?, ?, ?)");

    $profiles = [
        [
            'User Admin',
            'Full access to manage users and profiles',
            'Responsible for managing user accounts and profiles.'
        ],
        [
            'CSR Rep',
            'View and shortlist volunteer opportunities',
            'Acts on behalf of corporate volunteers (CVs) to coordinate with persons-in-need (PINs).'
        ],
        [
            'PIN',
            'Manage personal requests',
            'Person-in-need of receiving assistance from CSR volunteers.'
        ],
        [
            'Platform Manager',
            'Generate reports, manage volunteer services categories',
            'Supervises platform performance and ensures compliance with CSR objectives.'
        ]
    ];

    foreach ($profiles as $p) {
        $stmt->bind_param("sss", $p[0], $p[1], $p[2]);
        $stmt->execute();
    }
}

// make the categories table if it doesn't exist
$categoriesSQL = "
CREATE TABLE IF NOT EXISTS categories (
    categoryID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    status ENUM('Active','Inactive') DEFAULT 'Active'
);";

if (!$conn->query($categoriesSQL)) {
    die('Error creating categories table: ' . $conn->error);
}

// makes sample categories if count is 0
$checkCategory = $conn->query("SELECT COUNT(*) AS total FROM categories");
$catCount = $checkCategory->fetch_assoc()['total'];

if ($catCount == 0) {
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $categories = [
        ['Medical Assistance', 'Includes helping PINs to visit doctors, pharmacies, or hospitals.'],
        ['Transportation', 'Providing transport or mobility assistance for PINs.'],
        ['Household Help', 'Assistance with cleaning, groceries, or home maintenance.'],
        ['Companionship', 'Spending time or checking in with PINs who need emotional support.']
    ];
    foreach ($categories as $c) {
        $stmt->bind_param("ss", $c[0], $c[1]);
        $stmt->execute();
    }
}

// make the request table if it doesn't exist
$requestsSQL = "
CREATE TABLE IF NOT EXISTS requests (
    requestID INT AUTO_INCREMENT PRIMARY KEY,
    pinID INT,
    title VARCHAR(150),
    description TEXT,
    categoryID INT,
    dateCreated DATETIME,
    status ENUM('Active','Completed','Cancelled') DEFAULT 'Active',
    views INT DEFAULT 0,
    shortlistedCount INT DEFAULT 0,
    FOREIGN KEY (pinID) REFERENCES users(userID),
    FOREIGN KEY (categoryID) REFERENCES categories(categoryID)
);";
if (!$conn->query($requestsSQL)) {
    die('Error creating requests table: ' . $conn->error);
}

// makes 100 sample requests if count is 0
$checkReq = $conn->query("SELECT COUNT(*) AS total FROM requests");
$reqCount = $checkReq->fetch_assoc()['total'];

if ($reqCount == 0) {
    // checks for the PIN users in the user table
    $pinResult = $conn->query("SELECT userID FROM users WHERE role = 'PIN'");
    $pinIDs = [];
    while ($row = $pinResult->fetch_assoc()) {
        $pinIDs[] = $row['userID'];
    }
    $pinCount = count($pinIDs);

    // count available categories
    $catResult = $conn->query("SELECT categoryID FROM categories");
    $catIDs = [];
    while ($row = $catResult->fetch_assoc()) {
        $catIDs[] = $row['categoryID'];
    }
    $catCount = count($catIDs);

    $stmt = $conn->prepare("
        INSERT INTO requests 
        (pinID, title, description, categoryID, dateCreated, status, views, shortlistedCount) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)
    ");

    $statuses = ['Active', 'Completed', 'Cancelled'];

    for ($i = 1; $i <= 100; $i++) {
        $pinID = $pinIDs[array_rand($pinIDs)];
        $title = "Request {$i}";
        $description = "Sample request decription {$i}.";
        $categoryID = $catIDs[array_rand($catIDs)];
        $status = $statuses[array_rand($statuses)];
        $views = rand(0, 30);
        $shortlistedCount = 0;
        $stmt->bind_param("issisii", $pinID, $title, $description, $categoryID, $status, $views, $shortlistedCount);
        $stmt->execute();
    }
}

// make the shortlist table if it doesn't exist
$shortlistSQL = "
CREATE TABLE IF NOT EXISTS shortlists (
    shortlistID INT AUTO_INCREMENT PRIMARY KEY,
    csrID INT,
    requestID INT,
    dateSaved DATETIME,
    FOREIGN KEY (csrID) REFERENCES users(userID),
    FOREIGN KEY (requestID) REFERENCES requests(requestID)
);";

if (!$conn->query($shortlistSQL)) {
    die('Error creating shortlists table: ' . $conn->error);
}

// make the matches table if it doesn't exist
$matchesSQL = "
CREATE TABLE IF NOT EXISTS matches (
    matchID INT AUTO_INCREMENT PRIMARY KEY,
    requestID INT,
    csrID INT,
    pinID INT,
    categoryID INT,
    status ENUM('Completed','Pending Review') DEFAULT 'Completed',
    FOREIGN KEY (requestID) REFERENCES requests(requestID),
    FOREIGN KEY (csrID) REFERENCES users(userID),
    FOREIGN KEY (pinID) REFERENCES users(userID),
    FOREIGN KEY (categoryID) REFERENCES categories(categoryID)
);";

if (!$conn->query($matchesSQL)) {
    die('Error creating matches table: ' . $conn->error);
}
?>