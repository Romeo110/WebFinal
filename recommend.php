<?php
// Start a session
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirect the user to the login page if not logged in
    header("location: login.php");
    exit;
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://kit.fontawesome.com/46b7ceee20.js" crossorigin="anonymous"></script>
        <title>FilmFinder - Movies</title>
        <link rel="stylesheet" href="css/recommend.css">
    </head>

    <body>
        <!-- horizontal navbar -->
        <div class="navbar">
            <div class="horizontal-navbar">
                <ul>
                    <li>
                        <a href="index.html" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bxs-home'></i>
                        </span>
                            <span class="item-txt">
                            Home
                        </span>
                        </a>
                    </li>
                    <li>
                        <a href="movies.html" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bx-movie'></i>
                        </span>
                            <span class="item-txt">
                            Movies
                        </span>
                        </a>
                    </li>
                    <li>
                        <a href="recommend.php" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bxs-dish'></i>
                        </span>
                            <span class="item-txt">
                            MadeForYou
                        </span>
                        </a>
                    </li>
                    <li>
                        <a href="watch_list.php" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bxs-user'></i>
                        </span>
                            <span class="item-txt">
                            WatchList
                        </span>
                        </a>
                    </li>
                    <li>
                        <a href="preferences.php" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bx-cog'></i>
                        </span>
                            <span class="item-txt">
                            Preferences
                        </span>
                        </a>
                    </li>
                  <li>
                    <a href="compare.html" class="nav-link">
                        <span class="item-icon">
                          <i class='bx bxl-deezer'></i>
                  </span>
                        <span class="item-txt">
                      CompareTool
                  </span>
                    </a>
                </li>
                    <!-- Additional menu items if needed -->
                </ul>
            </div>
        </div>


        <div class="main-content">
            <section id="recommendations">
                <h2>Recommended Movies</h2>
                <?php
      // Start a session
      session_start();

      $server = "localhost";
      $userid = "uw05kxucdm6hu";
      $pw = "n6zlygfdot3s";
      $db = "dboyek8cty39tn";
      $conn = new mysqli($server, $userid, $pw, $db);
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Prepare and execute query to fetch user preferences
      $user_id = $_SESSION["id"]; // Assuming you store user ID in the session
      $query = "SELECT genre, actor_director, min_decade, max_decade, language FROM preferences WHERE user_id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $user_id); // 'i' for integer
      $stmt->execute();

      // Fetch preferences (assuming only one row)
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      if ($row) {
          // Access fetched preferences
          $genres = explode(', ', $row["genre"]);
          $actorDirectors = json_decode(json_decode($row["actor_director"], true), true);
          $minDecade = $row["min_decade"];
          $maxDecade = $row["max_decade"];
          $languages = json_decode(json_decode($row["language"], true), true);
      } else {
          echo "No preferences found for the user.";
          exit;
      }

      // Close statement and database connection
      $stmt->close();
      $conn->close();
      
      // Query TMDB API to fetch recommended movies based on user preferences
      $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
      $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Get current page number from URL query parameter or default to 1
      $itemsPerPage = 20; // Set the number of items per page
      $totalPages = PHP_INT_MAX;

      // Initialize an empty array to store all recommended movies
      $allRecommendedMovies = array();

      $allMovies = []; // Initialize an array to store all movies
      $actorIds = [];

      // Check if genres are not empty
      if (!empty($genres)) {
        foreach ($genres as $genre) {

          // Check if languages are not empty
          if (!empty($languages)) {
            foreach ($languages as $language) {

              if(!empty($actorDirectors)) {
                foreach ($actorDirectors as $actorDirector) {
                  $actor = urlencode($actorDirector); 
                  $actorSearchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$actor}";
                  $actorResponse = file_get_contents($actorSearchUrl);
                  $actorData = json_decode($actorResponse, true);
                  $actorId = isset($actorData['results'][0]['id']) ? $actorData['results'][0]['id'] : null;

                  $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
                  $discoverMoviesUrl .= "&with_genres={$genre}";
                  $discoverMoviesUrl .= "&language={$language}";
                  $discoverMoviesUrl .= "&with_people={$actorId}";
                  $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
                  if (!$minDecade == 0 && !$maxDecade == 0) {
                    $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
                    $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
                  }
                  $discoverMoviesUrl .= "&page={$currentPage}";

                  // Fetch movies based on the constructed URL
                  $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
                  $discoverMoviesData = json_decode($discoverMoviesResponse, true);

                  // Check if the fetched data is not empty and if it has the least total_pages
                  if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
                    $totalPages = $discoverMoviesData['total_pages'];
                  }

                  // Check if the fetched data is not empty
                  if (!empty($discoverMoviesData['results'])) {
                      // Merge the movies into the array of all movies
                      $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
                  }
                }
              } else {
                $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
                $discoverMoviesUrl .= "&with_genres={$genre}";
                $discoverMoviesUrl .= "&language={$language}";
                $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
                if (!$minDecade == 0 && !$maxDecade == 0) {
                  $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
                  $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
                }
                $discoverMoviesUrl .= "&page={$currentPage}";

                // Fetch movies based on the constructed URL (without language parameter)
                $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
                $discoverMoviesData = json_decode($discoverMoviesResponse, true);

                // Check if the fetched data is not empty and if it has the least total_pages
                if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
                  $totalPages = $discoverMoviesData['total_pages'];
                }

                // Check if the fetched data is not empty
                if (!empty($discoverMoviesData['results'])) {
                    // Merge the movies into the array of all movies
                    $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
                }
              }
            }
          } else {
            $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
            $discoverMoviesUrl .= "&with_genres={$genre}";
            $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
            if (!$minDecade == 0 && !$maxDecade == 0) {
              $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
              $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
            }
            $discoverMoviesUrl .= "&page={$currentPage}";

            // Fetch movies based on the constructed URL (without language parameter)
            $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
            $discoverMoviesData = json_decode($discoverMoviesResponse, true);

            // Check if the fetched data is not empty and if it has the least total_pages
            if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
              $totalPages = $discoverMoviesData['total_pages'];
            }

            // Check if the fetched data is not empty
            if (!empty($discoverMoviesData['results'])) {
                // Merge the movies into the array of all movies
                $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
            }
          }
        }
      // Check if languages are not empty
      } else if (!empty($languages)) {
        foreach ($languages as $language) {

          if(!empty($actorDirectors)) {
            foreach ($actorDirectors as $actorDirector) {
              $actor = urlencode($actorDirector); 
              $actorSearchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$actor}";
              $actorResponse = file_get_contents($actorSearchUrl);
              $actorData = json_decode($actorResponse, true);
              $actorId = isset($actorData['results'][0]['id']) ? $actorData['results'][0]['id'] : null;

              $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
              $discoverMoviesUrl .= "&language={$language}";
              $discoverMoviesUrl .= "&with_people={$actorId}";
              $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
              if (!$minDecade == 0 && !$maxDecade == 0) {
                $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
                $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
              }
              $discoverMoviesUrl .= "&page={$currentPage}";

              // Fetch movies based on the constructed URL
              $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
              $discoverMoviesData = json_decode($discoverMoviesResponse, true);

              // Check if the fetched data is not empty and if it has the least total_pages
              if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
                $totalPages = $discoverMoviesData['total_pages'];
              }

              // Check if the fetched data is not empty
              if (!empty($discoverMoviesData['results'])) {
                  // Merge the movies into the array of all movies
                  $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
              }
            }
          } else {
            $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
            $discoverMoviesUrl .= "&language={$language}";
            $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
            if (!$minDecade == 0 && !$maxDecade == 0) {
              $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
              $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
            }
            $discoverMoviesUrl .= "&page={$currentPage}";

            // Fetch movies based on the constructed URL (without language parameter)
            $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
            $discoverMoviesData = json_decode($discoverMoviesResponse, true);

            // Check if the fetched data is not empty and if it has the least total_pages
            if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
              $totalPages = $discoverMoviesData['total_pages'];
            }

            // Check if the fetched data is not empty
            if (!empty($discoverMoviesData['results'])) {
                // Merge the movies into the array of all movies
                $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
            }
          }
        }
      } else if(!empty($actorDirectors)) {
        foreach ($actorDirectors as $actorDirector) {
          $actor = urlencode($actorDirector); 
          $actorSearchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$actor}";
          $actorResponse = file_get_contents($actorSearchUrl);
          $actorData = json_decode($actorResponse, true);
          $actorId = isset($actorData['results'][0]['id']) ? $actorData['results'][0]['id'] : null;

          $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}";
          $discoverMoviesUrl .= "&with_people={$actorId}";
          $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
          if (!$minDecade == 0 && !$maxDecade == 0) {
            $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
            $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
          }
          $discoverMoviesUrl .= "&page={$currentPage}";

          // Fetch movies based on the constructed URL
          $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
          $discoverMoviesData = json_decode($discoverMoviesResponse, true);

          // Check if the fetched data is not empty and if it has the least total_pages
          if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
            $totalPages = $discoverMoviesData['total_pages'];
          }

          // Check if the fetched data is not empty
          if (!empty($discoverMoviesData['results'])) {
              // Merge the movies into the array of all movies
              $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
          }
        }
      } else {
              // Fetch movies based on the default URL (without genre or language parameter)
              $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie";
              $discoverMoviesUrl .= "?api_key={$apiKey}";
              $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
              if (!$minDecade == 0 && !$maxDecade == 0) {
                $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
                $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
              }
              $discoverMoviesUrl .= "&page={$currentPage}";

              $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
              $discoverMoviesData = json_decode($discoverMoviesResponse, true);

              // Check if the fetched data is not empty and if it has the least total_pages
              if (!empty($discoverMoviesData['total_pages']) && $discoverMoviesData['total_pages'] < $totalPages) {
                $totalPages = $discoverMoviesData['total_pages'];
              }

              // Check if the fetched data is not empty
              if (!empty($discoverMoviesData['results'])) {
                  // Merge the movies into the array of all movies
                  $allMovies = array_merge($allMovies, $discoverMoviesData['results']);
              }
          }

      // Remove duplicates from the merged array of all movies
      $allMovies = array_unique($allMovies, SORT_REGULAR);

      // Display recommended movies
      if (!empty($allMovies)) {
        foreach ($allMovies as $movie) {
          echo '<div class="movie-card">';
          echo '<div class="movie-details">';
          echo '<div class="movie-poster">';
          // Add link to movie details page with movie ID as query parameter
          echo '<a href="movie_details.php?movieId=' . $movie['id'] . '">';
          echo '<img src="https://image.tmdb.org/t/p/w342/' . $movie['poster_path'] . '" alt="' . $movie['title'] . ' Poster" class="movie-poster">';
          echo '</a>';
          echo '</div>';
          
          // Add link to movie details page with movie ID as query parameter
          echo '<div class="movie-info">';
          echo '<h3><a href="movie_details.php?movieId=' . $movie['id'] . '">' . $movie['title'] . '</a></h3>';
          echo '<p>Release Date: ' . $movie['release_date'] . '</p>';
          echo '<p>Rating: ' . $movie['vote_average'] . '</p>';
          echo '<p>Description: ' . $movie['overview'] . '</p>'; // Include movie description
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }

        // Pagination controls
        // $totalPages = ceil(count($allMovies) / $itemsPerPage);
        echo '<div class="pagination">';
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '">Previous</a>';
        }
        echo ' Page ' . $currentPage . '/' . $totalPages . ' ';
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '">Next</a>';
        }
        echo '</div>';
      } else {
          echo '<p>No recommended movies found for the selected preferences.</p>';
      }
      ?>
            </section>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="background">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100%" viewBox="0 0 1600 900">
          <defs>
              <linearGradient id="bg" x2="0%" y2="100%">
                  <stop
                      offset="0%"
                      style="stop-color: #642b5b"
                  ></stop>
                  <stop
                      offset="100%"
                      style="stop-color: #fdf6fc"
                  ></stop>
              </linearGradient>
              <path
                  id="wave"
                  fill="url(#bg)"
                  d="M-363.852,502.589c0,0,236.988-41.997,505.475,0
      s371.981,38.998,575.971,0s293.985-39.278,505.474,5.859s493.475,48.368,716.963-4.995v560.106H-363.852V502.589z"
              />
          </defs>
          <g>
              <use xlink:href="#wave" opacity=".3">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="8s"
                      calcMode="spline"
                      values="270 230; -334 180; 270 230"
                      keyTimes="0; .5; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
              <use xlink:href="#wave" opacity=".6">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="6s"
                      calcMode="spline"
                      values="-270 230;243 220;-270 230"
                      keyTimes="0; .6; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
              <use xlink:href="#wave" opacty=".9">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="4s"
                      calcMode="spline"
                      values="0 230;-140 200;0 230"
                      keyTimes="0; .4; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
          </g>
          <text x="50%" y="93%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="15">
              &copy; 2024 FilmFinder. All rights reserved.
          </text>
      </svg>
            </div>

        </footer>
        <script>
            // footer swap
            window.addEventListener('scroll', function() {
                var scrollPosition = window.scrollY;
                var windowHeight = window.innerHeight;
                var documentHeight = document.documentElement.scrollHeight;
                var threshold = 30;

                // Check if the scrollbar is at the bottom of the page
                if (scrollPosition + windowHeight >= documentHeight - threshold) {
                    // Show animated footer
                    document.body.classList.add('show-footer');
                } else {
                    // Hide animated footer
                    document.body.classList.remove('show-footer');
                }
            });
        </script>
    </body>

    </html>