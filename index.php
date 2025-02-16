<?php
session_start();

define("USER_DB", "users.json");

// Load users
function loadUsers() {
    if (!file_exists(USER_DB)) file_put_contents(USER_DB, json_encode([]));
    return json_decode(file_get_contents(USER_DB), true);
}

// Save users
function saveUsers($users) {
    file_put_contents(USER_DB, json_encode($users, JSON_PRETTY_PRINT));
}

// Register user
if (isset($_POST["register"])) {
    $users = loadUsers();
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    if (isset($users[$username])) {
        echo "User already exists!";
    } else {
        $users[$username] = $password;
        saveUsers($users);
        echo "Registration successful!";
    }
}

// Login user
if (isset($_POST["login"])) {
    $users = loadUsers();
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        $_SESSION["user"] = $username;
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid login!";
    }
}

// Logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherMajor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="taskbar">
        <div class="tabs-container">
            <div class="tabs" id="tabs">
                <div class="tab active" id="main-tab">Main</div>
            </div>
        </div>
        <button class="add-tab" onclick="addNewTab()">+</button>
        <?php if (isset($_SESSION["user"])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION["user"]) ?>! </span>
            <a href="?logout=true">Logout</a>
        <?php else: ?>
            <form method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <button type="submit" name="register">Register</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="main-content" id="mainContent">
        <iframe src="https://weather.im/iembot/" title="Weather IM"></iframe>
        <iframe src="https://battaglia.ddns.net/twc/" title="WeatherStar4000+"></iframe>
    </div>

    <script>
        function addNewTab() {
            let userUrl = prompt("Enter a weather-related URL:");
            if (!userUrl) return;
            let tabId = "tab-" + Math.random().toString(36).substr(2, 9);
            let tab = document.createElement("div");
            tab.className = "tab";
            tab.innerHTML = userUrl + ' <span onclick="this.parentElement.remove()">x</span>';
            document.getElementById("tabs").appendChild(tab);
        }
    </script>
</body>
</html>
