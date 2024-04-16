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
      // Hardcoded user preferences
      $preferences = array(
        'genre' => 'action',
        'year' => '2020',
        'language' => 'en', // English
        'min_rating' => 7, // Minimum rating out of 10
        'min_year' => 2010, // Minimum release year
        'actor' => 'Tom Hanks', // Favorite actor
        'director' => 'Christopher Nolan', // Favorite director
        // Add more preferences as needed
      );
      
      // Query TMDB API to fetch recommended movies based on user preferences
      $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
      $genreId = getGenreId($preferences['genre']);
      $year = $preferences['year'];
      $language = $preferences['language'];
      $minRating = $preferences['min_rating'];
      $minYear = $preferences['min_year'];
      $actor = urlencode($preferences['actor']); // URL encode actor name
      $director = urlencode($preferences['director']); // URL encode director name
      $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Get current page number from URL query parameter or default to 1
      
      // Search for movies featuring favorite actor
      $actorSearchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$actor}";
      $actorResponse = file_get_contents($actorSearchUrl);
      $actorData = json_decode($actorResponse, true);
      $actorId = isset($actorData['results'][0]['id']) ? $actorData['results'][0]['id'] : null;
      
      // Search for movies directed by favorite director
      $directorSearchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$director}";
      $directorResponse = file_get_contents($directorSearchUrl);
      $directorData = json_decode($directorResponse, true);
      $directorId = isset($directorData['results'][0]['id']) ? $directorData['results'][0]['id'] : null;
      
      // Fetch movies featuring favorite actor
      $actorMovies = array();
      if ($actorId) {
        $actorMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language={$language}&with_people={$actorId}&vote_average.gte={$minRating}&primary_release_date.gte={$minYear}&page={$currentPage}";
        $actorMoviesResponse = file_get_contents($actorMoviesUrl);
        $actorMoviesData = json_decode($actorMoviesResponse, true);
        if ($actorMoviesData && isset($actorMoviesData['results'])) {
          $actorMovies = $actorMoviesData['results'];
        }
      }
      
      // Fetch movies directed by favorite director
      $directorMovies = array();
      if ($directorId) {
        $directorMoviesUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language={$language}&with_crew={$directorId}&vote_average.gte={$minRating}&primary_release_date.gte={$minYear}&page={$currentPage}";
        $directorMoviesResponse = file_get_contents($directorMoviesUrl);
        $directorMoviesData = json_decode($directorMoviesResponse, true);
        if ($directorMoviesData && isset($directorMoviesData['results'])) {
          $directorMovies = $directorMoviesData['results'];
        }
      }
      
      // Merge and display recommended movies
      $recommendedMovies = array_merge($actorMovies, $directorMovies);
      if (!empty($recommendedMovies)) {
        echo '<ul>';
        foreach ($recommendedMovies as $movie) {
          echo '<li>' . $movie['title'] . '</li>';
        }
        echo '</ul>';
        
        // Pagination controls
        $totalPages = max(isset($actorMoviesData['total_pages']) ? $actorMoviesData['total_pages'] : 1, isset($directorMoviesData['total_pages']) ? $directorMoviesData['total_pages'] : 1);
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
        echo '<p>No recommended movies found.</p>';
      }
      
      // Function to get genre ID based on genre name
      function getGenreId($genreName) {
        $genres = array(
          'action' => 28,
          'comedy' => 35,
          'drama' => 18,
          // Add more genres as needed
        );
        return isset($genres[$genreName]) ? $genres[$genreName] : null;
      }
      ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2024 FilmFinder. All rights reserved.</p>
  </footer>
</body>
</html>
