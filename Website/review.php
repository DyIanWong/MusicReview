<?php
    $submitted = false;
    $song_id = '';
    $written_review = '';
    $tags_review = '';
    $stars_review = '';

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

    // Check if a form has been submitted
    if (isset($_POST['review'])) {
        // Check if database is open
        if ($db = validateDatabase('song-covers.db')) {
            $song_id = intval($_GET['id']);
            $written_review = $_POST['written-review'];
            $tags_review = '';
            $stars_review = $_POST['star-rating'];

            // Concatenate each selected tag into comma-separated string
            if (isset($_POST['tags'])) {
                $selected_tags = array();
                foreach ($_POST['tags'] as $val) {
                    $selected_tags[] = $val;
                }

                // Format tags
                $tags_review = implode(', ', $selected_tags);
            }

            // Send data into database if at least a star review has been provided
            if (!($tags_review === '' && $written_review === '' && $tags_review === '' && $stars_review === '') && !($stars_review === '')) {
                // Send a command using SQL to manipulate database and close
                $sql = "INSERT INTO song_reviews (song_id, tag_reviews, star_reviews, written_reviews) VALUES ($song_id, '$tags_review', '$stars_review', '$written_review')";

                $db->exec($sql);
                $db->close();

                $submitted = true;
            }
        }
    }

    ?>
<html style="padding: 0;">
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
    <body class="position-relative d-block pb-5">
        <?php
            // Establishment of a link to the .db file through creating an object
            if ($submitted) {
                echo "<div id=\"popup-full\" class=\"position-absolute w-100 h-100\" style=\"z-index: 9998; background-color: #181818ab\"><div id=\"popup\" class=\"position-relative bg-first h-25 w-25 top-50 translate-middle start-50 rounded text-white shadow\" style=\"z-index: 9999\"><div class=\"position-absolute w-100 text-center top-50 start-50 translate-middle fs-2\" id=\"closetext\">Review submitted. Thanks!</div><span id=\"close\" style=\"color: #ffffff;\" class=\"float-end p-4\"><i class=\"fas fa-2x fa-xmark\"></i></span></div></div>";
            };

            $db = new SQLite3('song-covers.db');

            // Examination of link to .db file and return an error message if unsuccessful
            if (!$db) {
                echo $db->lastErrorMsg();
                exit();
            } else {
                $song_covers = $db->query('SELECT * FROM song_covers;');
                $result = $db->query("SELECT * FROM song_covers WHERE id={$_GET['id']};");
                $song_cover = $result->fetchArray();
                $tags = $db->query('SELECT * FROM tags;');
            }  
        ?>
        <!-- Bootstrap JS and PopperJS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
        <!-- Navigation -->
        <div class="d-block bg-first px-5 py-5 shadow position-sticky bottom-0 top-0" style="height: 10vh; z-index: 999;">
            <div class="d-inline-flex h-100 float-start w-50 align-items-center" href="indexfa.php">
                <a class="px-2 py-3" style="width: 37.5%" href="index.php"><img src="media/images/logo.png" class="w-100"></a>
            </div>
            <div class="d-inline-flex h-100 w-25 float-end flex-row-reverse align-items-center">
                <div class="p-4 fs-3 nav-button"><a href="login.php" class="nav-button text-white text-decoration-none">Statistics</a></div>
                <div class="dropdown">
                    <button class="text-white nav-button transition text-decoration-none border px-4 py-3 fs-3 bg-first rounded shadow dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Review Songs</button>
                    <div class="dropdown-menu py-0" style="" aria-labelledby="dropdownMenuButton1">
                        <?php
                            // Generate a new div for each element in song_covers array
                            while ($row = $song_covers->fetchArray()) {
                                echo "<a class=\"dropdown-item position-relative pe-auto px-4 py-4 w-100 border-0 border-top\" href=\"review.php?id={$row["id"]}\" style=\"margin: 0; cursor: pointer;\">{$row["song_name"]}</a>";
                            };
                        ?>
                    </div>
                </div>
                <div class="p-4 fs-3 nav-button"><a href="index.php" class="nav-button text-white text-decoration-none">Home</a></div>
            </div>
        </div>

        <!-- Review section -->
        <div class="mx-auto my-5 py-5 d-flex justify-content-end align-items-center" style="width: 75%;">
            <div class="py-5 flex-grow-1">
            <?php
                echo "<h1 class=\"display-1 mb-0\" style=\"font-weight: 500\">{$song_cover['song_name']}</h1>"
            ?>
            </div>
            <div class="dropdown">
                <button class="text-white nav-button transition my-auto text-decoration-none border py-3 fs-3 bg-first rounded-pill shadow dropdown-toggle" style="padding: 7rem" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Select song</button>
                <div class="dropdown-menu py-0 w-100" aria-labelledby="dropdownMenuButton1">
                    <?php
                        // Generate a new div for each element in song_covers array
                        while ($row = $song_covers->fetchArray()) {
                            echo "<a class=\"dropdown-item text-center pe-auto px-4 py-4 w-100 border-0 border-top\" href=\"review.php?id={$row["id"]}\" style=\"margin: 0; cursor: pointer;\">{$row["song_name"]}</a>";
                        };
                    ?>
                </div>
            </div>
        </div>

        <!-- Form -->
        <?php
            echo "<form id=\"reviewform\" action=\"review.php?id={$_GET['id']}\" method=\"post\" class=\"d-flex mx-auto bg-first rounded\" style=\"padding: 3.5rem; width: 75%; height: 60rem\">";
        ?>
            <div class="flex-grow-1 me-5 d-inline-flex flex-column h-100">
                <div class="dropdown w-75 mb-5">
                    <button class="text-white nav-button transition my-auto text-decoration-none border py-3 px-3 w-100 fs-3 bg-first rounded shadow dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Select tag(s)</button>
                    <div class="dropdown-menu py-0 w-100"  aria-labelledby="dropdownMenuButton1">
                        <div class="text-center pe-auto px-4 py-4 border-0 border-top d-flex gap-3 flex-wrap w-100">
                            <?php
                                // Generate a new div for each element in song_covers array
                                while ($row = $tags->fetchArray()) {
                                    echo "<input class=\"d-none checkbox-input\" value=\"{$row['tag_name']}\" id=\"tag{$row['id']}\" name=\"tags[]\" type=\"checkbox\"></input><label onclick=\"select({$row['id']})\" class=\"checkbox-label me-2 d-block review-button px-4 py-3 transition rounded border border-2\" style=\"border-color: rgba(139, 139, 139, .3); color: #919191;\" for=\"tag{$row['id']}\" id=\"label{$row['id']}\">{$row['tag_name']}</label>";
                                };
                            ?>
                        </div>
                    </div>
                </div>
                <div class="mb-5 rating d-inline-flex justify-content-end flex-row-reverse">
                    <input type="hidden" id="star-rating" name="star-rating">
                    <?php
                        // Display 5 stars with corresponding values
                        for ($i = 5; $i > 0; $i--) {
                            echo "<span><input type=\"radio\" name=\"rating\" id=\"star{$i}\" value=\"{$i}\" class=\"d-none\"><label for=\"star{$i}\"><i class=\"fas fa-5x fa-star\"></i></label></span>";
                        }
                    ?>
                </div>
                <div style="flex: 1">
                    <textarea maxlength="250" placeholder="Give us a review..." name="written-review" class="rounded h-100 w-100 px-4 py-3 fs-4 border-0" style="outline: none"></textarea>
                </div>
            </div>
            <div class="w-25 d-inline-flex flex-column justify-content-between">
                <?php
                    // Dynamically generate an audio player according to song id
                    echo "<div class=\"d-block text-center\"><img src=\"{$song_cover["cover_image_url"]}\" class=\"w-100\">";
                        echo <<< EOS
                        <audio class="mt-5 w-100" style="height: 4rem;" controls controlsList="nodownload noplaybackrate">
                            <source src="$song_cover[7]" type="audio/mpeg">
                        </audio>
EOS;
                ?>
                    <div class="mt-3 text-danger fs-5" style="display: none" id="warning">Please provide a star rating</div>
                </div>
                <div class="text-center">
                    <input name="review" class=" py-3 fs-2 px-2 border-0 border-bottom border-1 review-button transition bg-first text-white" type="submit" value="Submit Review">
                </div>
            </div>
        </form>
    </body>
    <script>
        // Examine user click in order to close tags divs
        $(document).ready(function () {
            $("body").click(function(event) {
                if (event.target.id != "search") {
                    $('#search-results').css("display", "none");
                }

                $('.dropdown-toggle').on('click', function(event) {
                    event.stopPropagation();
                });

                $('.dropdown-menu').on('click', function(event) {
                    event.stopPropagation();
                });
            });
        });

        // For each star element, check the final star which has been checked and return its value
        // Input: click on star
        // Output: count of star
        function countStars() {
            var count = 0;
            for (var i = 1; i < 6; i++) {
                if (document.getElementById(`star${i}`).checked) {
                    count = document.getElementById(`star${i}`).value;
                }
            }
            return count;
        }


        $(document).ready(function () {
            var url = window.location.href;
            // Check Radio-box
            $(".rating input:radio").attr("checked", false);

            // Update URL string upon input to stars
            $('.rating input').click(function () {
                $(".rating span").removeClass('checked');
                $(this).parent().addClass('checked');
                $("#star-rating").attr("value", $(this).val());
            });

            // Check if popup has been closed
            $('body').click(function(e) {
                $('#popup-full').css('display', 'none');
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
    <?php

        // Validate if the user has provided at least a star rating for a review
        if (isset($_POST['review']) && (($tags_review === '' && $written_review === '' && $tags_review === '' && $stars_review === '') || ($stars_review === ''))) {
            echo <<< SCRIPT
                <script>
                    $("#reviewform").css("border", "2px solid #ff0000");
                    $("#warning").css("display", "block");
                </script>
SCRIPT;
        }
    ?>
</html>