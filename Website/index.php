<!DOCTYPE html>
<html>
    <!--
    Title: MusicReview
    Author: Dylan W.
    Date of creation: 28/6/22
    Date of completion: 14/8/22
    -->
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MusicReview</title>
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
        <?php
            // Establishment of a link to the .db file through creating an object
            $db = new SQLite3('song-covers.db');

            // Examination of link to .db file and return an error message if unsuccessful
            // Prevent further code from running until db is opened properly
            if (empty($db) == true) {
                echo $db->lastErrorMsg();
                exit();
            } else {
                $song_covers = $db->query('SELECT * FROM song_covers;');
            }
        ?>

        <!-- Bootstrap JS and PopperJS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
        
        <!-- Navigation -->
        <div class="d-block bg-first px-5 py-5 shadow position-sticky bottom-0 top-0" style="height: 10vh">
            <div class="d-inline-flex float-start h-100 w-50 align-items-center">
                <a class="px-2 py-3" href="index.php" style="width: 37.5%"><img src="media/images/logo.png" class="w-100"></a>
                <div class="position-relative p-4">
                    <form class="p-4" name="search" method="get" action="">
                        <input class="rounded-pill px-4 py-3 rounded border-0 fs-3 shadow" type="text" name="search" id="search" placeholder="Search..." autocomplete="off"></input>
                        <div class="position-absolute shadow table mt-1" style="width: 140%" id="search-results">
                            <?php
                                /* 
                                Collect form data from "search" then determine if data is undefined or empty. If data is valid, then send a query to the .db file for records matching
                                user query. For each row, create a div.
                                */

                                if (ISSET($_GET['search']) && empty($_GET['search']) == false) {
                                    $query = htmlspecialchars($_GET['search']);
                                    $matched_song_covers = $db->query("SELECT * FROM song_covers WHERE instr(lower(song_name), lower('$query'));");

                                    // Find the length of the rows
                                    $rows = $db->query("SELECT COUNT(*) as count FROM song_covers WHERE instr(lower(song_name), lower('$query'));");
                                    $row = $rows->fetchArray();
                                    $matched_length = $row['count'];

                                    if ($matched_length !== 0) {
                                        while ($row = $matched_song_covers->fetchArray()) {
                                            echo "<a href=\"review.php?id={$row['id']}\" class=\"px-2 py-4 border-0 border-top w-100 d-block text-decoration-none text-center\" value=\"{$query}\" style=\"background-color: #ffffff\"><p style=\"margin: 0\">{$row["song_name"]}</p></a>";
                                        };
                                    } else {
                                        echo "<div class=\"px-2 py-4 border-0 border-top w-100 text-center\" style=\"background-color: #ffffff\"><p style=\"margin: 0\">No result.</p></div>";
                                    }
                                }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-inline-flex h-100 w-25 float-end flex-row-reverse align-items-center">
                <div class="p-4 fs-3 nav-button"><a href="login.php" class="nav-button text-white text-decoration-none">Statistics</a></div>
                <div class="dropdown">
                    <button class="text-white nav-button transition text-decoration-none border px-4 py-3 fs-3 bg-first rounded shadow dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Review Songs</button>
                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton1">
                        <?php
                            // Generate a new div for each element in song_covers array
                            while ($row = $song_covers->fetchArray()) {
                                echo "<a class=\"dropdown-item pe-auto px-4 py-4 w-100 border-0 border-top\" href=\"review.php?id={$row["id"]}\" style=\"margin: 0; cursor: pointer;\">{$row["song_name"]}</a>";
                            };
                        ?>
                    </div>
                </div>
                <div class="p-4 fs-3 nav-button"><a href="" class="nav-button text-white text-decoration-none">Home</a></div>
            </div>
        </div>

        <!-- Main content -->
        <div class="w-75 mx-auto my-5 p-5">
            <div class="mx-auto d-block py-5" style="width: 90%;">
                <h1 class="display-1" style="font-weight: 500">Song Covers</h1>
            </div>
            <div class="d-block mx-auto p-5 bg-first shadow my-5 rounded text-white" style="width: 90%">
                <div class="d-flex flex-wrap">
                <?php

                    // Generate a new div for each element in song_covers array
                    while ($row = $song_covers->fetchArray()) {
                        echo <<< EOS
                            <div class="p-4" style="flex: 50%">
                                <div class="d-block"><img src="{$row["cover_image_url"]}" class="w-100"></div>
                                <div class="d-block my-5"><h1>{$row["song_name"]}</h1><h4>from {$row["source"]} ({$row["year"]})</h4></div>
                                <div class="d-block mb-4"><p>{$row["description"]}</p></div>
                                <a href="review.php?id={$row["id"]}" class="d-inline-block text-decoration-none border-bottom py-3 px-2 fs-5 border-white text-white review-button transition">Review now <i class="fa-solid fa-angles-right"></i></a>
                            </div>
EOS;
                    };
                ?>
                </div>
            </div>
        </div>
        <script>
            // Examination of user click in order to close search results
            $(document).ready(function () {
                // Input: click
                // Output: close search results
                $("body").click(function(event) {
                    if (event.target.id != "search") {
                        $('#search-results').css("display", "none");
                    }
                });

                // Add slideDown animation to Bootstrap dropdown when expanding
                $('.dropdown').on('show.bs.dropdown', function() {
                    $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150, 'swing');
                });

                // Add slideUp animation to Bootstrap dropdown when collapsing
                $('.dropdown').on('hide.bs.dropdown', function() {
                    $(this).find('.dropdown-menu').first().stop(true, true).slideUp(150, 'swing');
                });
            });
        </script>
    </body>
</html>