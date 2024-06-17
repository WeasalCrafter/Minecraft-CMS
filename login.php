<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["password"] === "isles2002") {
        $_SESSION["loggedin"] = true;
        header("location: index.php");
        exit;
    } else {
        $login_err = "Invalid password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft CMS</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="password" placeholder="Password" name="password">
            <button type="submit" class="button">Login</button>
            <?php
            if (isset($login_err)) {
                echo "<p style='color: red;'>$login_err</p>";
            }
            ?>
        </form>
    </div>
</body>
</html>
