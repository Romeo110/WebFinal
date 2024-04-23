<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Preference Form</title>
    <style>
        .preference-section {
            margin-bottom: 20px;
        }

        .preference-section input[type="text"] {
            width: 200px;
            padding: 5px;
            margin-bottom: 5px;
        }

        .preference-section .dropdown {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            width: 200px;
            z-index: 1;
        }

        .preference-section .dropdown li {
            list-style-type: none;
            cursor: pointer;
            padding: 5px;
        }

        .preference-section .dropdown li:hover {
            background-color: #f0f0f0;
        }

        .preference-section .selected-items {
            margin-bottom: 10px;
        }

        .preference-section .selected-items span {
            background-color: #f0f0f0;
            padding: 5px;
            border-radius: 5px;
            margin-right: 5px;
        }

        .preference-section .selected-items span button {
            margin-left: 5px;
            cursor: pointer;
            background-color: #ff4d4d;
            border: none;
            border-radius: 50%;
            color: white;
            padding: 2px 6px;
        }
    </style>
</head>
<body>
    <h1>Movie Preference Form</h1>
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
            <input type="text" name="min_decade" id="minDecade" placeholder="Enter minimum decade (e.g., 1990s)">
            <label for="maxDecade">To:</label>
            <input type="text" name="max_decade" id="maxDecade" placeholder="Enter maximum decade (e.g., 2010s)">
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
    </script>
</body>
</html>
