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
    <link rel="stylesheet" href="css/preferences.css">

    <title>Building Your Library</title>
</head>

<body>
    <!-- <h1>Movie Preference Form</h1> -->
    <!-- nav bar with only home button -->
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

    <div class="container">
        <!-- Introductory Card -->
        <div class="card">
            <div class="card-body">
                <h2>What Is This Form?</h2>
                <p class="intro-paragraph">FilmFinder offers you the opportunity to tailor your movie recommendations by sharing your preferences. This form allows you to specify your favorite movie genres, preferred actors or directors, desired movie release decades, and preferred
                    languages. Your inputs will assist us in curating personalized movie suggestions tailored to your tastes and preferences. Simply fill out the form below to enhance your FilmFinder experience and discover movies that resonate with you
                    on a deeper level.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="process_preferences.php" method="post">
                    <fieldset>
                        <legend>Favorite Genres</legend>
                        <?php
                // Fetch the list of genres from the TMDB API
                $apiKey = 'd5697eb16a89b204a004af1f8fea130c'; // Replace with your actual API key
                $genresUrl = "https://api.themoviedb.org/3/genre/movie/list?api_key=$apiKey&language=en-US";
                $genresResponse = file_get_contents($genresUrl);
                $genresData = json_decode($genresResponse, true);

                if (isset($genresData['genres'])) {
                    $genres = $genresData['genres'];
                    foreach ($genres as $genre) {
                        echo "<label><input type='checkbox' class='genreCheckbox' name='genres[]' value='{$genre['id']}'> {$genre['name']}</label><br>";
                    }
                } else {
                    echo "Failed to fetch genres.";
                }
            ?>
                            <label><input type="checkbox" id="noPreferenceGenre" name="no_preference_genre" value="no_preference"> No Preference</label>
                    </fieldset>
                    <br><br>
                    <fieldset class="preference-section">
                        <legend>Favorite Actor/Director</legend>
                        <div class="selected-items" id="selectedActorsDirectors"></div>
                        <input type="text" name="actors_directors[]" id="actor_director" placeholder="Enter actor/director name" multiple>
                        <input type="hidden" name="selected_actors_directors" id="selectedActorsDirectorsInput">
                        <ul id="actorDirectorDropdown" class="dropdown"></ul>
                        <label><input type="checkbox" id="noPreferenceActorDirector" name="no_preference_actor_director" value="no_preference"> No Preference</label>
                    </fieldset>
                    <br><br>
                    <fieldset>
                        <legend>Decade Preference</legend>
                        <label for="minDecade">From:</label>
                        </br>
                        <input type="text" name="min_decade" id="minDecade" placeholder="Enter minimum decade (e.g., 1990s)">
                        <label for="maxDecade"></br>To:</label>
                        </br>
                        <input type="text" name="max_decade" id="maxDecade" placeholder="Enter maximum decade (e.g., 2010s)">
                        </br>
                        <label><input type="checkbox" id="noPreferenceDecade" name="no_preference_decade" value="no_preference"> No Preference</label>
                    </fieldset>
                    <br><br>
                    <fieldset class="preference-section">
                        <legend>Language Preference</legend>
                        <div class="selected-items" id="selectedLanguages"></div>
                        <input type="text" id="languageInput" placeholder="Type a language">
                        <input type="hidden" name="selected_languages" id="selectedLanguagesInput">
                        <ul id="languageDropdown" class="dropdown"></ul>
                        <label><input type="checkbox" id="noPreferenceLanguage" name="no_preference_language" value="no_preference"> No Preference</label>
                    </fieldset>
                    <br><br>
                    <input type="submit" value="Submit Preferences">
                </form>
            </div>
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
        document.addEventListener("DOMContentLoaded", function() {
            // Genre section
            const genreCheckboxes = document.querySelectorAll(".genreCheckbox");
            const noPreferenceGenreCheckbox = document.getElementById("noPreferenceGenre");

            noPreferenceGenreCheckbox.addEventListener("change", function() {
                genreCheckboxes.forEach(function(checkbox) {
                    checkbox.disabled = this.checked;
                    checkbox.checked = false;
                }, this);
            });

            // Language section
            const languageInput = document.getElementById("languageInput");
            const selectedLanguages = document.getElementById("selectedLanguages");
            const languageDropdown = document.getElementById("languageDropdown");
            const noPreferenceLanguageCheckbox = document.getElementById("noPreferenceLanguage");

            // Array to store selected languages
            const selectedLanguageSet = new Set();

            // Function to update the selected languages and the hidden input field
            function updateSelectedLanguages() {
                selectedLanguages.innerHTML = "";
                const selectedLanguagesArray = Array.from(selectedLanguageSet); // Convert set to array
                selectedLanguagesArray.forEach(language => {
                    const span = document.createElement("span");
                    span.textContent = language;
                    const button = document.createElement("button");
                    button.textContent = "x";
                    button.addEventListener("click", function() {
                        selectedLanguageSet.delete(language);
                        updateSelectedLanguages();
                    });
                    span.appendChild(button);
                    selectedLanguages.appendChild(span);
                });

                // Update the hidden input field with the selected languages
                document.getElementById("selectedLanguagesInput").value = JSON.stringify(selectedLanguagesArray);
            }

            // Fetch list of languages from TMDB API
            fetch("https://api.themoviedb.org/3/configuration/languages?api_key=d5697eb16a89b204a004af1f8fea130c")
                .then(response => response.json())
                .then(data => {
                    const languages = data.map(language => language.english_name);

                    // Function to filter languages based on user input
                    function filterLanguages(input) {
                        return languages.filter(language => language.toLowerCase().startsWith(input.toLowerCase()));
                    }

                    // Function to display filtered languages in the dropdown
                    function displayFilteredLanguages(input) {
                        const filteredLanguages = filterLanguages(input);
                        languageDropdown.innerHTML = "";
                        filteredLanguages.forEach(language => {
                            const li = document.createElement("li");
                            li.textContent = language;
                            li.addEventListener("click", function() {
                                selectedLanguageSet.add(language);
                                updateSelectedLanguages();
                                languageInput.value = "";
                                languageDropdown.style.display = "none";
                            });
                            languageDropdown.appendChild(li);
                        });
                        if (filteredLanguages.length > 0) {
                            languageDropdown.style.display = "block";
                        } else {
                            languageDropdown.style.display = "none";
                        }
                    }

                    // Event listener for language input
                    languageInput.addEventListener("input", function() {
                        const inputText = this.value.trim();
                        if (inputText !== "") {
                            displayFilteredLanguages(inputText);
                        } else {
                            languageDropdown.style.display = "none";
                        }
                    });

                    // Event listener for "No Preference" checkbox
                    noPreferenceLanguageCheckbox.addEventListener("change", function() {
                        if (this.checked) {
                            languageInput.value = "";
                            languageInput.disabled = true;
                            languageDropdown.style.display = "none";
                            selectedLanguages.innerHTML = "";
                            selectedLanguageSet.clear();
                        } else {
                            languageInput.disabled = false;
                        }
                    });
                })
                .catch(error => {
                    console.error("Error fetching languages:", error);
                });

            // Actor/Director section
            const actorDirectorInput = document.getElementById("actor_director");
            const actorDirectorDropdown = document.getElementById("actorDirectorDropdown");
            const selectedActorsDirectors = document.getElementById("selectedActorsDirectors");
            const noPreferenceActorDirectorCheckbox = document.getElementById("noPreferenceActorDirector");

            const selectedActorDirectorSet = new Set();

            // Function to update the selected actors/directors and the hidden input field
            function updateSelectedActorsDirectors() {
                selectedActorsDirectors.innerHTML = "";
                const selectedActorsDirectorsArray = Array.from(selectedActorDirectorSet); // Convert set to array
                selectedActorsDirectorsArray.forEach(actorDirector => {
                    const span = document.createElement("span");
                    span.textContent = actorDirector;
                    const button = document.createElement("button");
                    button.textContent = "x";
                    button.addEventListener("click", function() {
                        selectedActorDirectorSet.delete(actorDirector);
                        updateSelectedActorsDirectors();
                    });
                    span.appendChild(button);
                    selectedActorsDirectors.appendChild(span);
                });

                // Update the hidden input field with the selected actors/directors
                document.getElementById("selectedActorsDirectorsInput").value = JSON.stringify(selectedActorsDirectorsArray);
            }

            actorDirectorInput.addEventListener("input", function() {
                const inputText = this.value.trim();
                if (inputText !== "") {
                    // Fetch list of actors/directors from TMDB API based on inputText
                    fetchActorsDirectors(inputText)
                        .then(data => {
                            // Clear previous options
                            actorDirectorDropdown.innerHTML = "";
                            // Populate dropdown with new options
                            data.forEach(actorDirector => {
                                const li = document.createElement("li");
                                li.textContent = actorDirector;
                                li.addEventListener("click", function() {
                                    // Add selected actor/director to the set
                                    selectedActorDirectorSet.add(actorDirector);
                                    // Update the hidden input field with selected actors/directors
                                    updateSelectedActorsDirectors();
                                    // Clear the input field
                                    actorDirectorInput.value = "";
                                    // Hide the dropdown
                                    actorDirectorDropdown.style.display = "none";
                                });
                                actorDirectorDropdown.appendChild(li);
                            });
                            // Display the dropdown
                            actorDirectorDropdown.style.display = "block";
                        })
                        .catch(error => {
                            console.error("Error fetching actors/directors:", error);
                        });
                } else {
                    // Clear actorDirectorDropdown
                    actorDirectorDropdown.innerHTML = "";
                    // Hide the dropdown
                    actorDirectorDropdown.style.display = "none";
                }
            });

            // Event listener for "No Preference" checkbox
            noPreferenceActorDirectorCheckbox.addEventListener("change", function() {
                if (this.checked) {
                    actorDirectorInput.value = "";
                    actorDirectorInput.disabled = true;
                    actorDirectorDropdown.style.display = "none";
                    selectedActorsDirectors.innerHTML = "";
                    selectedActorDirectorSet.clear();
                    // Update the hidden input field with selected actors/directors
                    updateSelectedActorsDirectors();
                } else {
                    actorDirectorInput.disabled = false;
                }
            });

            // Function to fetch list of actors/directors from TMDB API
            function fetchActorsDirectors(inputText) {
                // Use fetch API to make a request to the TMDB API
                const apiKey = 'd5697eb16a89b204a004af1f8fea130c';
                const url = `https://api.themoviedb.org/3/search/person?api_key=${apiKey}&query=${inputText}`;
                return fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Extract actor/director names from the response
                        return data.results.map(person => person.name);
                    });
            }

            // Decade section
            const minDecadeInput = document.getElementById("minDecade");
            const maxDecadeInput = document.getElementById("maxDecade");
            const noPreferenceDecadeCheckbox = document.getElementById("noPreferenceDecade");

            noPreferenceDecadeCheckbox.addEventListener("change", function() {
                minDecadeInput.disabled = this.checked;
                maxDecadeInput.disabled = this.checked;
                minDecadeInput.value = '';
                maxDecadeInput.value = '';
            });

            // Function to check if a string represents a valid date
            function isValidDate(dateString) {
                // Check if the string matches the format YYYY or YYYYs where s is 's', 's', 's', or 's'
                const regex = /^(19|20)\d{2}s?$/;
                return regex.test(dateString);
            }

            // Fetch the last page of results
            fetch("https://api.themoviedb.org/3/discover/movie?api_key=d5697eb16a89b204a004af1f8fea130c&language=en-US&sort_by=release_date.asc&page=1")
                .then(response => response.json())
                .then(data => {
                    const minYear = Math.min(...data.results.map(movie => parseInt(movie.release_date.substring(0, 4))));
                    console.log("Minimum release year:", minYear);

                    // Add event listener for form submission
                    document.querySelector("form").addEventListener("submit", function(event) {
                        // Validate decade input if preference is not selected
                        const noPreferenceDecadeCheckbox = document.getElementById("noPreferenceDecade");
                        if (!noPreferenceDecadeCheckbox.checked) {
                            const minDecadeInput = document.getElementById("minDecade");
                            const maxDecadeInput = document.getElementById("maxDecade");
                            const minDecade = parseInt(minDecadeInput.value);
                            const maxDecade = parseInt(maxDecadeInput.value);

                            // Validate min and max decade
                            if (isNaN(minDecade) || isNaN(maxDecade) || minDecade > maxDecade) {
                                alert("Invalid decade range. Please enter valid years for the decade range or select 'No Preference'.");
                                event.preventDefault(); // Prevent form submission
                                return;
                            }

                            // Validate min and max year
                            const minYear = Math.min(...data.results.map(movie => parseInt(movie.release_date.substring(0, 4))));
                            if ((minDecade < minYear || minDecade > new Date().getFullYear()) ||
                                (maxDecade < minYear || maxDecade > new Date().getFullYear())) {
                                alert("Invalid year range. Please enter valid years between " + minYear + " and " + new Date().getFullYear() + ".");
                                event.preventDefault(); // Prevent form submission
                                return;
                            }
                        }

                    });
                })
                .catch(error => {
                    console.error("Error fetching total number of pages:", error);
                });

            document.querySelector("form").addEventListener("submit", function(event) {
                // Validate genre selection
                const genreCheckboxes = document.querySelectorAll(".genreCheckbox");
                const selectedGenres = [...genreCheckboxes].filter(checkbox => checkbox.checked).length;
                const noPreferenceGenreCheckbox = document.getElementById("noPreferenceGenre");

                if (!selectedGenres && !noPreferenceGenreCheckbox.checked) {
                    alert("Please select at least one genre or choose 'No Preference'.");
                    event.preventDefault(); // Prevent form submission
                    return;
                }

                // Validate actor/director selection
                const selectedActorDirectorSet = new Set(document.getElementById("selectedActorsDirectors").innerText.split("\n").filter(Boolean));
                const noPreferenceActorDirectorCheckbox = document.getElementById("noPreferenceActorDirector");

                if (selectedActorDirectorSet.size === 0 && !noPreferenceActorDirectorCheckbox.checked) {
                    alert("Please select at least one actor/director or choose 'No Preference'.");
                    event.preventDefault(); // Prevent form submission
                    return;
                }

                // Validate language selection
                const selectedLanguageSet = new Set(document.getElementById("selectedLanguages").innerText.split("\n").filter(Boolean));
                const noPreferenceLanguageCheckbox = document.getElementById("noPreferenceLanguage");

                if (selectedLanguageSet.size === 0 && !noPreferenceLanguageCheckbox.checked) {
                    alert("Please select at least one language or choose 'No Preference'.");
                    event.preventDefault(); // Prevent form submission
                    return;
                }

                // Other validation checks for decades, if needed...

                // If all validations pass, the form will be submitted
            });

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
    </script>
</body>

</html>