<?php
    session_start();

    // Return error message is database is not open
    // Input: database variable
    // Output: error message (if not defined) or database (if defined)
    function validateDatabase($dbName) {
        $db = new SQLite3($dbName);

        if (!$db) {
            echo $db->lastErrorMsg();
        }

        return $db;
    }

    // Redirect user to statistics.php if login details are valid
    if ($db = validateDatabase('song-covers.db')) {
        $login = $db->query("SELECT * FROM login;");
        while ($row = $login->fetchArray()) {
            if (ISSET($_SESSION['username']) && ISSET($_SESSION['password']))  {
                if ($_SESSION['username'] === $row['username'] && $_SESSION['hashed_password'] === $row['password']) {
                    header("LOCATION: statistics.php?id=1");
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MusicReview</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSS -->
        <link rel="stylesheet" href="style.css">
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Boostrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    </head>
    <body>
        <a href="index.php" class="d-inline-block m-5 text-decoration-none text-second transition" id="return" style="font-size: 2rem; cursor: pointer">
            <i class="fa-solid fa-angles-left"></i> Return Back
        </a>
        <!-- Login section -->
        <form class="d-flex flex-column position-absolute bg-first h-50 start-50 top-50 translate-middle rounded w-50 text-white" method="post">
            <div class="p-5 text-center" style="font-size: 3rem">Login Required</div>
            <div class="text-center mb-5">
                <input id="username" name="username" class="w-50 p-3" type="text" placeholder="Username" autocomplete="off">
            </div>
            <div class="text-center">
                <input id="password" name="password" class="w-50 p-3" type="password" placeholder="Password" autocomplete="off">
            </div>
            <div class="flex-grow-1 text-center position-relative">
                <div class="position-absolute start-50 translate-middle text-danger" id="errormessage" style="top: 75%; display: none;">Incorrect username or password</div>
                <input id="login" name="login" class="w-25 p-3 position-absolute start-50 top-50 translate-middle" type="submit" value="Log in">
            </div>
        </form>
    </body>
</html>
<?php
    // Check if a form has been submitted
    if (isset($_POST['login'])) {
        // Check if database is open
        if ($db = validateDatabase('song-covers.db')) {
            $login = $db->query("SELECT * FROM login;");
            // Set SESSION global variables to avoid re-entering login details
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['password'] = $_POST['password'];
            $_SESSION['hashed_password'] = md5($_SESSION['password']);

            // Redirect user to statistics.php if login details is correct
            while ($row = $login->fetchArray()) {
                if ($row['username'] == $_SESSION['username'] && $row['password'] === $_SESSION['hashed_password']) {
                    header("LOCATION: statistics.php?id=1");
                } else {
                    // Warn user of invalid login details
                    echo <<< SCRIPT
                    <script>
                        $("#username").css("border", "1px solid #ff0000");
                        $("#password").css("border", "1px solid #ff0000");
                        $("#errormessage").css("display", "inline");
                    </script>
SCRIPT;
                }
            };
            $db->close();
        }
    }
?>