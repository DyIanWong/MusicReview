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

    // Redirect user back to login.php if login details invalid
    if ($db = validateDatabase('song-covers.db')) {
        $login = $db->query("SELECT * FROM login;");
        while ($row = $login->fetchArray()) {
            if (!ISSET($_SESSION['username']) && !ISSET($_SESSION['password']))  {
                header("LOCATION: login.php");
                exit();
            } elseif ($_SESSION['username'] !== $row['username'] || $_SESSION['hashed_password'] !== $row['password']) {
                header("LOCATION: login.php");
                exit();
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
        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js" integrity="sha512-TW5s0IT/IppJtu76UbysrBH9Hy/5X41OTAbQuffZFU6lQ1rdcLHzpU5BzVvr/YFykoiMYZVWlr/PX1mDcfM9Qg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0-rc.1/chartjs-plugin-datalabels.min.js" integrity="sha512-+UYTD5L/bU1sgAfWA0ELK5RlQ811q8wZIocqI7+K0Lhh8yVdIoAMEs96wJAIbgFvzynPm36ZCXtkydxu1cs27w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Easy pie chart -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.6/jquery.easypiechart.min.js" integrity="sha512-DHNepbIus9t4e6YoMBSJLwl+nnm0tIwMBonsQQ+W9NKN6gVVVbomJs9Ii3mQ+HzGZiU5FyJLdnAz9a63ZgZvTQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body class="pb-5">
        <?php
            // Establishment of a link to the .db file through creating an object
            $db = new SQLite3('song-covers.db');

            // Examination of link to .db file and return an error message if unsuccessful
            if (!$db) {
                echo $db->lastErrorMsg();
                exit();
            } else {
                // make the url have two properties
                $song_covers = $db->query('SELECT * FROM song_covers;');
                $result = $db->query("SELECT * FROM song_covers WHERE id={$_GET['id']};");
                $song_cover = $result->fetchArray();
                $reviews = $db->query("SELECT * FROM song_reviews WHERE song_id={$_GET['id']}");
            }
        ?>
        <!-- Bootstrap JS and PopperJS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <?php
            // Define initial quantity of star ratings chosen
            $ones = 0;
            $twos = 0;
            $threes = 0;
            $fours = 0;
            $fives = 0;
            $rating_count = 0;
            
            // Add one to the quantity of star ratings
            while ($row = $reviews->fetchArray()) {
                switch ($row['star_reviews']) {
                    case "1":
                        $ones++;
                        break;
                    case "2":
                        $twos++;
                        break;
                    case "3":
                        $threes++;
                        break;
                    case "4":
                        $fours++;
                        break;
                    case "5":
                        $fives++;
                        break;
                }
                $rating_count++;
            };

            // Return the average rating of stars
            // Input: all star ratings
            // Output: average star rating
            function averageRating($ones, $twos, $threes, $fours, $fives, $rating_count) {
                if ($rating_count != 0) {
                    return round(($ones*1 + $twos*2 + $threes*3 + $fours*4 + $fives*5) / $rating_count * 10) / 10;
                } else {
                    return 0;
                }
            }

            echo <<< SCRIPT
                    <script>var yValues = [{$ones}, {$twos}, {$threes}, {$fours}, {$fives}]</script>
SCRIPT;

        ?>

         <!-- Navigation -->
        <div class="d-block bg-first px-5 py-5 shadow position-sticky bottom-0 top-0" style="height: 10vh; z-index: 9999;">
            <div class="d-inline-flex h-100 float-start w-50 align-items-center">
                <a class="px-2 py-3" style="width: 37.5%" href="index.php"><img src="media/images/logo.png" class="w-100"></a>
            </div>
            <div class="d-inline-flex h-100 w-25 float-end flex-row-reverse align-items-center">
                <div class="p-4 fs-3 nav-button"><a href="statistics.php?id=1" class="nav-button text-white text-decoration-none">Statistics</a></div>
                <div class="dropdown">
                    <button class="text-white nav-button transition text-decoration-none border px-4 py-3 fs-3 bg-first rounded shadow dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">Review Songs</button>
                    <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton1">
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

         <!-- Change song -->
         <div class="mx-auto my-4 py-5 d-flex justify-content-end align-items-center" style="width: 75%;">
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
                            echo "<a class=\"dropdown-item text-center pe-auto px-4 py-4 w-100 border-0 border-top\" href=\"statistics.php?id={$row["id"]}\" style=\"margin: 0; cursor: pointer;\">{$row["song_name"]}</a>";
                        };
                    ?>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="mx-auto mb-5" style="width: 75%;">
            <div class="d-flex w-100 justify-content-center" style="margin-bottom: 6rem">
                <div class="w-50" style="margin-right: 8rem; height: 25rem">
                    <canvas id="star-distribution-chart"></canvas>
                </div>
                <div style="height: 25rem">
                    <?php
                        $average_rating = averageRating($ones, $twos, $threes, $fours, $fives, $rating_count);

                        // Give the percentage (out of 5) for the average rating
                        // Input: average rating and number of stars displayed
                        // Output: rounded percentage to the first digit (out of 5)
                        function percentage($averageRating, $stars) {
                            return round((float)($averageRating / $stars) * 100) . "%";
                        }

                        echo "
                            <div class=\"progress-chart position-relative\" data-percent=\"" . percentage($average_rating, 5) . "\">
                                <div class=\"position-absolute top-50 start-50 translate-middle display-2\" style=\"font-weight: 500;\">" . $average_rating . "</div>
                            </div>
                        ";
                    ?>
                </div>
            </div>
            <div class="p-5 display-5 bg-first d-block mt-5 text-white">Reviews</div>
            <div class="accordion" id="accordion">

                <?php
                    // For each review that has the correct song id, generate a div containing details of the review
                    while ($row = $reviews->fetchArray()) {
                        $stars = '';
                        $star_count = $row['star_reviews'];

                        // Dynamically generate front-end display of stars
                        // Validate whether a star rating has been provided at all
                        if ($star_count > 0) {
                            for ($i = 0; $i < 5; $i++) {
                                // Highlight selected stars with yellow
                                // Highlight unselected stars with dark blue
                                if ($star_count > 0) {
                                    $stars .= "<span style=\"color: #fbbc04;\"><i class=\"fas fa-2x fa-star\"></i></span>";
                                } else {
                                    $stars .= "<span style=\"color: #001435;\"><i class=\"fas fa-2x fa-star\"></i></span>";
                                }
                                $star_count--;
                            }
                        }

                        // Validate if star review has been provided
                        // If provided, display star rating (on text)
                        // If not provided, display placeholder
                        if ($row['star_reviews'] != '') {
                            $rating = "{$row['star_reviews']}/5";
                        } else {
                            $rating = "-/5";
                        }

                        // Validate if tag review has been provided
                        // If provided, display all selected tags
                        // If not provided, display placeholder
                        if ($row['tag_reviews'] != '') {
                            $tags = "{$row['tag_reviews']}";
                        } else {
                            $tags = "None";
                        }

                        // Validate if written review has been provided
                        // If provided, display writing
                        // If not provided, display placeholder
                        if ($row['written_reviews'] != '') {
                            $writing = "<i>\" </i>{$row['written_reviews']} <i> \"</i>";
                        } else {
                            $writing = "None";
                        }

                        // Display the div which contains review details
                        echo <<< EOS
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{$row['id']}">
                                    <button class="accordion-button collapsed" style="display: flex; height: 6rem;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{$row['id']}" aria-expanded="false" aria-controls="collapse-{$row['id']}">
                                        <div>{$stars}</div>
                                        <div class="text-end flex-grow-1 me-4">Click to read</div>
                                    </button>
                                </h2>
                                <div id="collapse-{$row['id']}" class="accordion-collapse collapse" aria-labelledby="heading-{$row['id']}">
                                    <div class="accordion-body p-5">
                                        <div class="review-part"><div style="font-size: 1.5rem; font-weight: 600;" class="text-first mb-3">Rating: </div><div class="fs-3" style="letter-spacing: 2px">{$rating}</div></div>
                                        <div class="review-part"><div style="font-size: 1.5rem; font-weight: 600;" class="text-first mb-3">Selected tag(s): </div><div class="fs-3">{$tags}</div></div>
                                        <div class="review-part"><div style="font-size: 1.5rem; font-weight: 600;" class="mb-3 text-first">Comment:</div><div class="fs-3">{$writing}</div></div>
                                    </div>
                                </div>
                            </div>
EOS;
                    }
                ?>
            </div>
        </div>
    </body>
    <script>
        // Add slideDown animation to Bootstrap dropdown when expanding.
        $('.dropdown').on('show.bs.dropdown', function() {
            $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150, 'swing');
        });

        // Add slideUp animation to Bootstrap dropdown when collapsing.
        $('.dropdown').on('hide.bs.dropdown', function() {
            $(this).find('.dropdown-menu').first().stop(true, true).slideUp(150, 'swing');
        });

        $(function() {
            $('.progress-chart').easyPieChart({
                size: 250,
                lineWidth: 20,
                scaleColor: false,

            });
        });

        // Bar chart specifications
        new Chart("star-distribution-chart", {
            type: "bar",
            data: {
                labels: [1, 2, 3, 4, 5],
                datasets: [{
                backgroundColor: ["#0466c8", "#0353a4", "#023e7d", "#002855", "#001845"],
                data: yValues,
                }]
            },
            options: {
                legend: {display: false},
                responsive: true,
                maintainAspectRatio: false,
                title: {
                display: true,
                text: "World Wine Production 2018"
                },
                animation: false,
                events: [],
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        color: "#000000",
                        text: "Rating Distribution",
                        font: {
                            weight: "500",
                            size: "15"
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: "#000000",
                            fontSize: 18,
                            stepSize: 1,
                            beginAtZero: true,
                        }
                    },
                    x: {
                        ticks: {
                            color: "#000000",
                            fontSize: 14,
                            stepSize: 1,
                            beginAtZero: true
                        }
                    }
                }
            }
        });
    </script>
</html>