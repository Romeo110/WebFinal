<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Preference Form</title>
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
            <label><input type="checkbox" id="noPreferenceCheckbox" name="no_preference" value="no_preference"> No Preference</label>
        </fieldset>
        <br><br>
        <label for="actor_director">Favorite Actor/Director:</label>
        <input type="text" name="actor_director" id="actor_director" placeholder="Enter actor/director name">
        <br><br>
        <label for="decade">Decade Preference:</label>
        <input type="text" name="decade" id="decade" placeholder="Enter decade preference (e.g., 1990s)">
        <br><br>
        <label for="language">Language Preference:</label>
        <select name="language" id="language">
            <!-- Populate options dynamically from the list of languages in the API -->
            <?php
                // Fetch list of languages from the API (replace this with your API call)
                $languages = ['English', 'Spanish', 'French', 'German', 'Italian'];
                
                // Iterate through languages and create option elements
                foreach ($languages as $language) {
                    echo "<option value='$language'>$language</option>";
                }
            ?>
        </select>
        <br><br>
        <input type="submit" value="Submit Preferences">
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const genreCheckboxes = document.querySelectorAll(".genreCheckbox");
            const noPreferenceCheckbox = document.getElementById("noPreferenceCheckbox");

            // Disable genre checkboxes when "No Preference" is checked
            noPreferenceCheckbox.addEventListener("change", function() {
                genreCheckboxes.forEach(function(checkbox) {
                    checkbox.disabled = this.checked;
                    checkbox.checked = false;
                }, this);
            });
        });
    </script>
</body>
</html>
