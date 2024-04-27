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
          $actorDirectors = json_decode($row["actor_director"], true);
          $minDecade = $row["min_decade"];
          $maxDecade = $row["max_decade"];

          // Convert language names to language codes using the reverse mapping array
          $languages = json_decode($row["language"], true);
          echo "Languages from database: ";
          print_r($languages);
      } else {
          echo "No preferences found for the user.";
      }

      // Close statement and database connection
      $stmt->close();
      $conn->close();
      
      // Query TMDB API to fetch recommended movies based on user preferences
      $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
      $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Get current page number from URL query parameter or default to 1
      $itemsPerPage = 20; // Set the number of items per page

      // Initialize an empty array to store all recommended movies
      $allRecommendedMovies = array();

      // Fetch list of languages from TMDB API and create a reverse mapping array
      $languageMap = array();
      $languagesResponse = file_get_contents("https://api.themoviedb.org/3/configuration/languages?api_key={$apiKey}");
      $languagesData = json_decode($languagesResponse, true);
      if ($languagesData) {
          foreach ($languagesData as $language) {
              $languageMap[$language['english_name']] = $language['iso_639_1'];
          }
      }
      // echo "Language: " . reset($languages) . "<br>";
      // foreach ($languages as &$language) {
      //   echo "Language: $language<br>"; // Add line break after each language
      //   $language = $languageMap[$language];
      //   echo "Language code: $language<br>"; // Add line break after each language code
      // }
      foreach ($actorDirectors as $actorDirector) {
        echo "Actor: $actorDirector<br>";
      }

      // Example nested loops structure
      // foreach ($genres as $genre) {
      //   foreach ($languages as $language) {
      //       foreach ($actorDirectors as $actorDirector) {
      //           $actor = urlencode($actorDirector);
      //           $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie";
      //           $discoverMoviesUrl .= "?api_key={$apiKey}";
      //           $discoverMoviesUrl .= "&language={$language}";
      //           $discoverMoviesUrl .= "&with_genres={$genre}";
      //           $discoverMoviesUrl .= "&with_people={$actorId}";
      //           $discoverMoviesUrl .= "&vote_average.gte=6"; // Minimum rating of 6
      //           $discoverMoviesUrl .= "&primary_release_date.gte={$minDecade}";
      //           $discoverMoviesUrl .= "&primary_release_date.lte={$maxDecade}";
      //           $discoverMoviesUrl .= "&page={$currentPage}";
    
      //           // Fetch movies based on the constructed URL
      //           $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
      //           $discoverMoviesData = json_decode($discoverMoviesResponse, true);
    
      //           // Check if the fetched data is not empty
      //           if (!empty($discoverMoviesData['results'])) {
      //               foreach ($discoverMoviesData['results'] as $movie) {
      //                   // Check if the movie already exists in the array
      //                   if (!in_array($movie, $allRecommendedMovies)) {
      //                       $allRecommendedMovies[] = $movie;
      //                   }
      //               }
      //           }
      //       }
      //   }
      // }

      foreach ($languages as $language) {
        $discoverMoviesUrl = "https://api.themoviedb.org/3/discover/movie";
        $discoverMoviesUrl .= "?api_key={$apiKey}";
        $discoverMoviesUrl .= "&language={$language}";
        $discoverMoviesUrl .= "&page={$currentPage}";

        // Fetch movies based on the constructed URL
        $discoverMoviesResponse = file_get_contents($discoverMoviesUrl);
        if ($discoverMoviesResponse === false) {
            echo "Failed to fetch movies from TMDB API.";
        } else {
            $discoverMoviesData = json_decode($discoverMoviesResponse, true);
            if ($discoverMoviesData === null) {
                echo "Failed to decode JSON response from TMDB API.";
            } else {
                // Add fetched movies to the allRecommendedMovies array
                if (!empty($discoverMoviesData['results'])) {
                    foreach ($discoverMoviesData['results'] as $movie) {
                        // Check if the movie already exists in the array
                        if (!in_array($movie, $allRecommendedMovies)) {
                            $allRecommendedMovies[] = $movie;
                        }
                    }
                } else {
                    echo "No movies found in TMDB API response.";
                }
            }
        }
      }

      // Display recommended movies
      if (!empty($allRecommendedMovies)) {
          foreach ($allRecommendedMovies as $movie) {
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
          $totalPages = ceil(count($allRecommendedMovies) / $itemsPerPage);
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
