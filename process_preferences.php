<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Preferences</title>
</head>
<body>
    <h1>Movie Preferences</h1>

    <?php
    // Check if preferences are submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<h2>Your Preferences:</h2>";

        // Display selected genres
        if (isset($_POST['genres']) && !empty($_POST['genres'])) {
            echo "<p><strong>Favorite Genres:</strong> ";
            $selectedGenres = $_POST['genres'];
            echo implode(', ', $selectedGenres);
            echo "</p>";
        } else {
            echo "<p>No favorite genres selected.</p>";
        }

        // Display selected actor/director
        // Retrieve selected actors/directors from the form submission
        $selectedActorsDirectors = json_decode($_POST['selected_actors_directors'], true) ?? [];

        // Check if any actors/directors were selected
        if (!empty($selectedActorsDirectors)) {
            // Output each selected actor/director
            echo "<h2>Selected Actors/Directors</h2>";
            echo "<ul>";
            foreach ($selectedActorsDirectors as $actorDirector) {
                echo "<li>$actorDirector</li>";
            }
            echo "</ul>";
        } else {
            // If no actors/directors were selected, display a message
            echo "<p>No actors/directors selected.</p>";
        }

        // Display selected decades
        if (isset($_POST['min_decade']) && isset($_POST['max_decade']) && !empty($_POST['min_decade']) && !empty($_POST['max_decade'])) {
            echo "<p><strong>Decade Preference:</strong> {$_POST['min_decade']} to {$_POST['max_decade']}</p>";
        } else {
            echo "<p>No decade preference selected.</p>";
        }

        // Display selected languages
        if (isset($_POST['languages']) && !empty($_POST['languages'])) {
            echo "<p><strong>Language Preference:</strong> ";
            $selectedLanguages = $_POST['languages'];
            echo implode(', ', $selectedLanguages);
            echo "</p>";
        } else {
            echo "<p>No language preference selected.</p>";
        }

    } else {
        echo "<p>No preferences submitted.</p>";
    }
    ?>
</body>
</html>
