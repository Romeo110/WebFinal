<?php
$server = "localhost";
$userid = "u0kg2ws5z36zq";
$pw = "rzuoxy5bnggz";
$db = "dbioookmqfj5gb";
$conn = new mysqli($server, $userid, $pw, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get movie ID from URL parameter
$movie_id = isset($_GET['movieId']) ? sanitize_input($_GET['movieId']) : '';

// Directly adding the movie to the watchlist
$user_id = 1; // Replace with the actual user_id value

// Use prepared statements to prevent SQL injection
$sql = "INSERT INTO favorite_movies (user_id, movie_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $movie_id);

if ($stmt->execute()) {
    echo "Movie added to your watchlist.";
} else {
    echo "Error adding movie to your watchlist: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie Details</title>
  <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if you have one -->
  <style>
    /* Additional CSS for cast images */
    .cast-item {
      display: inline-block;
      margin-right: 20px;
    }

    .cast-item img {
      display: block;
      width: 150px;
      height: auto;
      margin-bottom: 5px;
    }

    /* Additional CSS for reviews */
    .review {
      margin-top: 20px;
      border-top: 1px solid #ccc;
      padding-top: 20px;
    }

    .review p {
      margin: 0;
    }

    .review-author {
      font-weight: bold;
    }

    /* Additional CSS for Add to Watchlist button */
    .watchlist-button {
      margin-top: 20px;
    }

    .watchlist-button button {
      padding: 10px 20px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .watchlist-button button:hover {
      background-color: #0056b3;
    }

    /* Positioning for movie image and button */
    .movie-image {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <header>
    <h1>Movie Details</h1>
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
  <button onclick="goBack()">Back</button>
    <section id="movie-details">
      <!-- This is where the movie details will be displayed -->
    </section>
  </main>

  <footer>
    <p>&copy; 2024 FilmFinder. All rights reserved.</p>
  </footer>

  <script>

        function goBack() {
            window.history.back();
        }
        function addToWatchlist(movieId) {

        }
        
        document.addEventListener('DOMContentLoaded', function() {
            var addToWatchlistButtons = document.querySelectorAll('.add-to-watchlist-button');
            addToWatchlistButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var movieId = button.getAttribute('data-movie-id');
                    addToWatchlist(movieId);
                });
            });
        });
    
    // Function to extract movie ID from URL
    function getMovieIdFromUrl() {
      var queryString = window.location.search;
      var urlParams = new URLSearchParams(queryString);
      return urlParams.get('movieId');
    }

    // Function to fetch movie details by ID
    function fetchMovieDetails(movieId) {
      var apiKey = 'd5697eb16a89b204a004af1f8fea130c';
      var movieUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=en-US`;
      var creditsUrl = `https://api.themoviedb.org/3/movie/${movieId}/credits?api_key=${apiKey}`;
      var reviewsUrl = `https://api.themoviedb.org/3/movie/${movieId}/reviews?api_key=${apiKey}`;

      // Fetch movie details
      fetch(movieUrl)
        .then(response => response.json())
        .then(movieData => {
          // Fetch movie credits
          fetch(creditsUrl)
            .then(response => response.json())
            .then(creditsData => {
              // Fetch movie reviews
              fetch(reviewsUrl)
                .then(response => response.json())
                .then(reviewsData => {
                  displayMovieDetails(movieData, creditsData, reviewsData);
                })
                .catch(error => {
                  console.error('Error fetching movie reviews:', error);
                });
            })
            .catch(error => {
              console.error('Error fetching movie credits:', error);
            });
        })
        .catch(error => {
          console.error('Error fetching movie details:', error);
        });
    }

    // Function to display movie details
    function displayMovieDetails(movie, credits, reviews) {
      var movieDetailsContainer = document.getElementById('movie-details');
      movieDetailsContainer.innerHTML = ''; // Clear previous movie details

      // Create elements to display movie details
      var title = document.createElement('h2');
      title.textContent = movie.title;
      var overview = document.createElement('p');
      overview.textContent = `Overview: ${movie.overview}`;
      var releaseDate = document.createElement('p');
      releaseDate.textContent = `Release Date: ${movie.release_date}`;
      var rating = document.createElement('p');
      rating.textContent = `Rating: ${movie.vote_average}`;
      var popularity = document.createElement('p');
      popularity.textContent = `Popularity: ${movie.popularity}`;
      var language = document.createElement('p');
      language.textContent = `Original Language: ${movie.original_language}`;
      var runtime = document.createElement('p');
      runtime.textContent = `Runtime: ${movie.runtime} minutes`;
      var budget = document.createElement('p');
      budget.textContent = `Budget: $${movie.budget}`;
      var revenue = document.createElement('p');
      revenue.textContent = `Revenue: $${movie.revenue}`;
      var status = document.createElement('p');
      status.textContent = `Status: ${movie.status}`;
      var tagline = document.createElement('p');
      tagline.textContent = `Tagline: ${movie.tagline}`;

      // Display movie image
      var movieImage = document.createElement('img');
      movieImage.classList.add('movie-image'); // Add class for styling
      movieImage.src = `https://image.tmdb.org/t/p/w342${movie.poster_path}`;
      movieImage.alt = `${movie.title} Poster`;
      movieDetailsContainer.appendChild(movieImage);

      // Add to Watchlist button
      var watchlistButtonContainer = document.createElement('div');
      watchlistButtonContainer.classList.add('watchlist-button');
      var addToWatchlistButton = document.createElement('button');
      addToWatchlistButton.textContent = 'Add to Watchlist';
      addToWatchlistButton.addEventListener('click', function() {
        // Logic to add the movie to the watchlist goes here
        alert('Movie added to watchlist!');
      });
      watchlistButtonContainer.appendChild(addToWatchlistButton);
      movieDetailsContainer.appendChild(watchlistButtonContainer);

      // Append movie details to container
      movieDetailsContainer.appendChild(title);
      movieDetailsContainer.appendChild(overview);
      movieDetailsContainer.appendChild(releaseDate);
      movieDetailsContainer.appendChild(rating);
      movieDetailsContainer.appendChild(popularity);
      movieDetailsContainer.appendChild(language);
      movieDetailsContainer.appendChild(runtime);
      movieDetailsContainer.appendChild(budget);
      movieDetailsContainer.appendChild(revenue);
      movieDetailsContainer.appendChild(status);
      movieDetailsContainer.appendChild(tagline);

      // Display cast images
      var castHeading = document.createElement('h3');
      castHeading.textContent = 'Cast';
      movieDetailsContainer.appendChild(castHeading);

      credits.cast.slice(0, 4).forEach(member => {
        var castItem = document.createElement('div');
        castItem.classList.add('cast-item');

        var castImage = document.createElement('img');
        castImage.src = member.profile_path ? `https://image.tmdb.org/t/p/w185${member.profile_path}` : 'placeholder.jpg';
        castImage.alt = member.name;
        castItem.appendChild(castImage);

        var castName = document.createElement('p');
        castName.textContent = `${member.name} as ${member.character}`;
        castItem.appendChild(castName);

        movieDetailsContainer.appendChild(castItem);
      });

      // Display reviews
      var reviewsHeading = document.createElement('h3');
      reviewsHeading.textContent = 'Reviews';
      movieDetailsContainer.appendChild(reviewsHeading);

      reviews.results.slice(0, 4).forEach(review => {
        var reviewDiv = document.createElement('div');
        reviewDiv.classList.add('review');

        var reviewContent = document.createElement('p');
        reviewContent.textContent = review.content;
        reviewDiv.appendChild(reviewContent);

        var reviewAuthor = document.createElement('p');
        reviewAuthor.classList.add('review-author');
        reviewAuthor.textContent = `- ${review.author}`;
        reviewDiv.appendChild(reviewAuthor);

        movieDetailsContainer.appendChild(reviewDiv);
      });
    }
        
    document.addEventListener('DOMContentLoaded', function() {
        var movieId = getMovieIdFromUrl();
        console.log('Movie ID:', movieId); // Log movieId to console
        if (movieId) {
            localStorage.setItem('movie_id', movieId); // Store movieId in local storage
            fetchMovieDetails(movieId);
        } else {
            console.error('Movie ID not found in URL.');
        }
    });
  </script>
</body>
</html>

