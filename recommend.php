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
        <li><a href="movies.html">Movie Details</a></li>
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
        // Add more preferences as needed
      );
      
      // Query TMDB API to fetch recommended movies based on user preferences
      $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
      $genreId = getGenreId($preferences['genre']);
      $year = $preferences['year'];
      $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_genres={$genreId}&primary_release_year={$year}";
      $response = file_get_contents($apiUrl);
      $data = json_decode($response, true);
      
      // Display recommended movies
      if ($data && isset($data['results']) && count($data['results']) > 0) {
        echo '<ul>';
        foreach ($data['results'] as $movie) {
          echo '<li>' . $movie['title'] . '</li>';
        }
        echo '</ul>';
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
