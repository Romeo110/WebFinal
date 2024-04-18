<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Preference Form</title>
    <style>
        #selectedLanguages {
            margin-bottom: 10px;
        }

        #selectedLanguages span {
            background-color: #f0f0f0;
            padding: 5px;
            border-radius: 5px;
            margin-right: 5px;
        }

        #selectedLanguages span button {
            margin-left: 5px;
            cursor: pointer;
            background-color: #ff4d4d;
            border: none;
            border-radius: 50%;
            color: white;
            padding: 2px 6px;
        }

        #languageDropdown {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            width: 200px;
            z-index: 1;
        }

        #languageDropdown li {
            list-style-type: none;
            cursor: pointer;
            padding: 5px;
        }

        #languageDropdown li:hover {
            background-color: #f0f0f0;
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
        <fieldset>
            <legend>Favorite Actor/Director</legend>
            <label for="actor_director">Favorite Actor/Director:</label>
            <input type="text" name="actor_director" id="actor_director" placeholder="Enter actor/director name">
            <label><input type="checkbox" id="noPreferenceActorDirector" name="no_preference_actor_director" value="no_preference"> No Preference</label>
        </fieldset>
        <br><br>
        <fieldset>
            <legend>Decade Preference</legend>
            <label for="decade">Decade Preference:</label>
            <input type="text" name="decade" id="decade" placeholder="Enter decade preference (e.g., 1990s)">
            <label><input type="checkbox" id="noPreferenceDecade" name="no_preference_decade" value="no_preference"> No Preference</label>
        </fieldset>
        <br><br>
        <fieldset>
            <legend>Language Preference</legend>
            <div id="selectedLanguages"></div>
            <input type="text" id="languageInput" placeholder="Type a language">
            <ul id="languageDropdown"></ul>
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

            // Actor/Director section
            const actorDirectorInput = document.getElementById("actor_director");
            const noPreferenceActorDirectorCheckbox = document.getElementById("noPreferenceActorDirector");

            noPreferenceActorDirectorCheckbox.addEventListener("change", function() {
                actorDirectorInput.disabled = this.checked;
                actorDirectorInput.value = '';
            });

            // Decade section
            const decadeInput = document.getElementById("decade");
            const noPreferenceDecadeCheckbox = document.getElementById("noPreferenceDecade");

            noPreferenceDecadeCheckbox.addEventListener("change", function() {
                decadeInput.disabled = this.checked;
                decadeInput.value = '';
            });

            // Language section
            // const languageSelect = document.getElementById("language");
            // const noPreferenceLanguageCheckbox = document.getElementById("noPreferenceLanguage");

            // noPreferenceLanguageCheckbox.addEventListener("change", function() {
            //     languageSelect.disabled = this.checked;
            //     languageSelect.selectedIndex = 0; // Reset to default option
            // });

            const languageInput = document.getElementById("languageInput");
            const selectedLanguages = document.getElementById("selectedLanguages");
            const languageDropdown = document.getElementById("languageDropdown");
            const noPreferenceLanguageCheckbox = document.getElementById("noPreferenceLanguage");

            // Array to store selected languages
            const selectedLanguageSet = new Set();

            // Function to update the selected languages display
            function updateSelectedLanguages() {
                selectedLanguages.innerHTML = "";
                selectedLanguageSet.forEach(language => {
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
        });
    </script>
</body>
</html>
