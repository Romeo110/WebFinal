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
  <title>FilmFinder - Recommendations</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <h1>FilmFinder</h1>
    <nav>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="#">Comparison</a></li>
        <li><a href="recommend.php">Recommendations</a></li>
        <li><a href="movies.html">Movies</a></li>
        <li><a href="#">Profile</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section id="recommendations">
      <h2>Recommended Movies</h2>
      <?php
      // Start a session
      session_start();

      $server = "localhost";
      $userid = "uw05kxucdm6hu";
      $pw = "n6zlygfdot3s";
      $db = "dbbyejddos2r5c";
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
              echo '<div class="movie">';
              echo '<div class="movie-details">';
              echo '<img src="https://image.tmdb.org/t/p/w342/' . $movie['poster_path'] . '" alt="' . $movie['title'] . ' Poster" class="movie-poster">';
              echo '<div class="movie-info">';
              echo '<h3>' . $movie['title'] . '</h3>';
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
  </main>

  <footer>
    <p>&copy; 2024 FilmFinder. All rights reserved.</p>
  </footer>
</body>
</html>
