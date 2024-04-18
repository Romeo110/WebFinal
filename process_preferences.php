<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processed Preferences</title>
</head>
<body>
    <h1>Processed Preferences</h1>
    <?php
        // Retrieve user preferences from the form submission
        $genre = $_POST['genre'];
        $actorDirector = $_POST['actor_director'];
        $decade = $_POST['decade'];
        $language = $_POST['language'];

        // Display the user's preferences
        echo "<p>Favorite Genre: $genre</p>";
        echo "<p>Favorite Actor/Director: $actorDirector</p>";
        echo "<p>Decade Preference: $decade</p>";
        echo "<p>Language Preference: $language</p>";
    ?>
</body>
</html>
