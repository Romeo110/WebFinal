<?php
// Initialize the session and database connection variables
 $server = "localhost";
        $userid = "u0kg2ws5z36zq";
        $pw = "rzuoxy5bnggz";
        $db = "dbioookmqfj5gb";
        $conn = new mysqli($server, $userid, $pw, $db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is coming from AJAX for user preferences
if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    $user_id = $_POST['user_id'] ?? '';

    // Prevent SQL injection, validate and sanitize $user_id here
    
    $sql = "SELECT movie_name, movie_description FROM favorite_movies WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $preferences = $result->fetch_all(MYSQLI_ASSOC);
    
    // Close the connection
    $stmt->close();
    $conn->close();
    
    // Return the preferences as JSON
    echo json_encode($preferences);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Preferences</title>
    <style>
        /* Your CSS styles go here */
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get user ID from local storage
            var userId = localStorage.getItem('user_id');

            // Check if we have the user ID
            if (userId) {
                // Prepare form data
                var formData = new FormData();
                formData.append('user_id', userId);
                formData.append('ajax', 1); // Indicate this is an AJAX request

                // Create and send an AJAX POST request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true); // POST to the same URL of this page
                xhr.onload = function () {
                    if (this.status >= 200 && this.status < 300) {
                        var preferences = JSON.parse(this.response);
                        var container = document.getElementById('preferencesContainer');
                        preferences.forEach(function(preference) {
                            var div = document.createElement('div');
                            div.className = 'preference-item';
                            div.innerHTML = '<h3>' + preference.movie_name + '</h3><p>' + preference.movie_description + '</p>';
                            container.appendChild(div);
                        });
                    } else {
                        console.error(this.statusText);
                    }
                };
                xhr.onerror = function () {
                    console.error(this.statusText);
                };
                xhr.send(formData);
            }
        });
    </script>
</head>
<body>
    <div id="preferencesContainer">
        <!-- Movie preferences will be displayed here -->
    </div>
</body>
</html>
