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

    // generate first 4 fixed samples then random the other 96
    $presetUsers = [
        ['UserAdmin', 'ua@example.com', 'password', 'User Admin', 'Active'],
        ['Pin', 'pin@example.com', 'password', 'PIN', 'Active'],
        ['CSRRep', 'csrr@example.com', 'password', 'CSR Rep', 'Active'],
        ['PlatformManager', 'pm@example.com', 'password', 'Platform Manager', 'Active']
    ];

    foreach ($presetUsers as $p) {
        $stmt->bind_param("sssss", $p[0], $p[1], $p[2], $p[3], $p[4]);
        $stmt->execute();
    }

    for ($i = 5; $i <= 100; $i++) {
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
    $stmt = $conn->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
    // generate 4 fixed categories then random the other 96
    $baseCats = [
        ['Medical Assistance', 'Includes helping PINs to visit doctors, pharmacies, or hospitals.', 'Active'],
        ['Transportation', 'Providing transport or mobility assistance for PINs.', 'Active'],
        ['Household Help', 'Assistance with cleaning, groceries, or home maintenance.', 'Active'],
        ['Companionship', 'Spending time or checking in with PINs who need emotional support.', 'Active']
    ];
    foreach ($baseCats as $c) {
        $stmt->bind_param("sss", $c[0], $c[1], $c[2]);
        $stmt->execute();
    }

    $statuses = ['Active', 'Inactive'];
    for ($i = 5; $i <= 100; $i++) {
        $name = "Category{$i}";
        $description = "Description for Category{$i}";
        $status = $statuses[array_rand($statuses)];
        $stmt->bind_param("sss", $name, $description, $status);
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
    dateCreated DATE,
    status ENUM('Cancelled','Active','Matched','Completed') DEFAULT 'Active',
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
    $pins = $conn->query("SELECT userID FROM users WHERE role = 'PIN'");
    $pinIDs = array_column($pins->fetch_all(MYSQLI_ASSOC), 'userID');

    // checks for the categories in the categories table
    $cats = $conn->query("SELECT categoryID FROM categories");
    $catIDs = array_column($cats->fetch_all(MYSQLI_ASSOC), 'categoryID');

    $stmt = $conn->prepare("
        INSERT INTO requests (pinID, title, description, categoryID, dateCreated, status, views, shortlistedCount)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)
    ");
    $statuses = ['Cancelled', 'Active', 'Matched', 'Completed'];

    for ($i = 1; $i <= 100; $i++) {
        $pinID = $pinIDs[array_rand($pinIDs)];
        $title = "Request {$i}";
        $description = "Sample request description {$i}.";
        $categoryID = $catIDs[array_rand($catIDs)];
        $status = $statuses[array_rand($statuses)];
        $views = rand(0, 30);
        $shortlist = 0;
        $stmt->bind_param("issisii", $pinID, $title, $description, $categoryID, $status, $views, $shortlist);
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
    dateMatched DATE,
    dateCompleted DATE,
    status ENUM('Matched','Completed') DEFAULT 'Matched',
    FOREIGN KEY (requestID) REFERENCES requests(requestID),
    FOREIGN KEY (csrID) REFERENCES users(userID),
    FOREIGN KEY (pinID) REFERENCES users(userID),
    FOREIGN KEY (categoryID) REFERENCES categories(categoryID)
);";
if (!$conn->query($matchesSQL)) {
    die('Error creating matches table: ' . $conn->error);
}

// create samples in the match table for request that are matched or completed in the request table
$checkMatch = $conn->query("SELECT COUNT(*) AS total FROM matches");
$matchCheck = $checkMatch->fetch_assoc()['total'];

if ($matchCheck == 0) {
    $csrResult = $conn->query("SELECT userID FROM users WHERE role = 'CSR Rep'");
    $csrIDs = array_column($csrResult->fetch_all(MYSQLI_ASSOC), 'userID');

    $reqResult = $conn->query("SELECT * FROM requests WHERE status IN ('Matched','Completed')");
    $stmt = $conn->prepare("
        INSERT INTO matches (requestID, csrID, pinID, categoryID, dateMatched, dateCompleted, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    while ($r = $reqResult->fetch_assoc()) {
        $csrID = $csrIDs[array_rand($csrIDs)];
        $matchDate = date("Y-m-d", strtotime("-" . rand(8, 60) . " days")); // keep matched older
        $completedDate = ($r['status'] == 'Completed')
            ? date("Y-m-d", strtotime("-" . rand(1, 7) . " days"))
            : null;
        $status = $r['status'];
        $stmt->bind_param(
            "iiiisss",
            $r['requestID'],
            $csrID,
            $r['pinID'],
            $r['categoryID'],
            $matchDate,
            $completedDate,
            $status
        );
        $stmt->execute();
    }
}