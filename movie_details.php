<?php
$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dbbyejddos2r5c";
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

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  // Redirect the user to the login page if not logged in
  header("location: login.php");
  exit;
}

// Get movie ID from URL parameter
$movie_id = isset($_GET['movieId']) ? sanitize_input($_GET['movieId']) : '';

// Directly adding the movie to the watchlist
$user_id = $_SESSION["id"];

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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/46b7ceee20.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vibrant.js/2.1.2/Vibrant.min.js"></script>

    <title>About the Movie</title>
    <link rel="stylesheet" href="css/movie_details.css">
</head>

<body>
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
                <!-- Additional menu items if needed -->
            </ul>
        </div>
    </div>



    <div class="main-content">
        <button onclick="goBack()">Back</button>
        <div id="backdrop-container"></div>

        <div class="movie-details-container">
            <section id="movie-details">
                <!-- This is where the movie details will be displayed -->
            </section>
        </div>


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
            // var movieId = 934632;
            var apiKey = 'd5697eb16a89b204a004af1f8fea130c';
            var movieUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=en-US`;
            var creditsUrl = `https://api.themoviedb.org/3/movie/${movieId}/credits?api_key=${apiKey}`;
            var reviewsUrl = `https://api.themoviedb.org/3/movie/${movieId}/reviews?api_key=${apiKey}`;
            var backdropUrl = `https://api.themoviedb.org/3/movie/${movieId}/images?api_key=${apiKey}`;

            // Fetch movie backdrops
            fetch(backdropUrl)
                .then(response => response.json())
                .then(backdropData => {
                    // Process the backdrop data
                    displayMovieBackdrops(backdropData);
                })
                .catch(error => {
                    console.error('Error fetching movie backdrops:', error);
                });

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


        function displayMovieBackdrops(backdrops) {
            // Assuming you have a container element with id 'backdrop-container' to display the backdrops
            var backdropContainer = document.getElementById('backdrop-container');
            backdropContainer.innerHTML = ''; // Clear previous backdrops

            // Loop through the first 6 backdrops and create image elements
            for (let i = 0; i < Math.min(6, backdrops.backdrops.length); i++) {
                var backdropImage = document.createElement('img');
                backdropImage.src = `https://image.tmdb.org/t/p/original${backdrops.backdrops[i].file_path}`; // Use 'original' size for high resolution
                backdropImage.alt = 'Backdrop Image';
                backdropImage.classList.add('backdrop-image'); // Add a class for styling
                if (i === 0) {
                    backdropImage.classList.add('large'); // Add class to make the first image larger
                }
                backdropContainer.appendChild(backdropImage);
            }

        }


        // Function to display movie details
        function displayMovieDetails(movie, credits, reviews) {
            var movieDetailsContainer = document.getElementById('movie-details');
            movieDetailsContainer.innerHTML = ''; // Clear previous movie details

            // Create elements to display movie details
            var title = document.createElement('h2');
            title.textContent = movie.title;
            title.classList.add('movie-title'); // Add a class for styling

            var overview = document.createElement('p');
            overview.textContent = `${movie.overview}`;
            overview.classList.add('movie-overview'); // Add a class for styling

            var releaseDate = document.createElement('p');
            releaseDate.textContent = `Release Date: ${movie.release_date}`;
            releaseDate.classList.add('movie-release-date'); // Add a class for styling

            var rating = document.createElement('p');
            rating.textContent = `Rating: ${movie.vote_average}`;
            rating.classList.add('movie-rating'); // Add a class for styling

            var popularity = document.createElement('p');
            popularity.textContent = `Popularity: ${movie.popularity}`;
            popularity.classList.add('movie-popularity'); // Add a class for styling

            var language = document.createElement('p');
            language.textContent = `Original Language: ${movie.original_language}`;
            language.classList.add('movie-language'); // Add a class for styling

            var runtime = document.createElement('p');
            runtime.textContent = `Runtime: ${movie.runtime} minutes`;
            runtime.classList.add('movie-runtime'); // Add a class for styling

            var budget = document.createElement('p');
            budget.textContent = `Budget: $${movie.budget}`;
            budget.classList.add('movie-budget'); // Add a class for styling

            var revenue = document.createElement('p');
            revenue.textContent = `Revenue: $${movie.revenue}`;
            revenue.classList.add('movie-revenue'); // Add a class for styling

            var status = document.createElement('p');
            status.textContent = `Status: ${movie.status}`;
            status.classList.add('movie-status'); // Add a class for styling

            var tagline = document.createElement('p');
            tagline.textContent = `${movie.tagline}`;
            tagline.classList.add('movie-tagline'); // Add a class for styling


            // Display movie image
            var movieImage = document.createElement('img');
            movieImage.classList.add('movie-image'); // Add class for styling
            movieImage.src = `https://image.tmdb.org/t/p/w342${movie.poster_path}`;
            movieImage.alt = `${movie.title} Poster`;
            // movieDetailsContainer.appendChild(movieImage);



            // Append movie details to container
            movieDetailsContainer.appendChild(title);

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

        document.addEventListener('DOMContentLoaded', function() {
            var backdropContainer = document.getElementById('backdrop-container');

            // Add event listener to each backdrop image
            backdropContainer.addEventListener('click', function(event) {
                var clickedImage = event.target;

                // Check if the clicked element is an image
                if (clickedImage.tagName === 'IMG' && clickedImage.classList.contains('backdrop-image')) {
                    // Move the clicked image to the first position in the container
                    backdropContainer.insertBefore(clickedImage, backdropContainer.firstChild);

                    // Remove 'large' class from all images
                    var allImages = document.querySelectorAll('.backdrop-image');
                    allImages.forEach(image => {
                        image.classList.remove('large');
                    });

                    // Add 'large' class to the clicked image
                    clickedImage.classList.add('large');
                }
            });

            // Your existing code to fetch and display backdrop images...
        });
    </script>
</body>

</html>