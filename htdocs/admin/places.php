<?php
// Predefined districts of Andhra Pradesh
$ap_districts = [
    "Visakhapatnam", "Vijayawada", "Kakinada", "Guntur", "Nellore", "Chittoor", "Kadapa", "Anantapur", 
    "Krishna", "East Godavari", "West Godavari", "Prakasam", "Srikakulam", "Vizianagaram", "Kurnool", 
    "Rayalaseema", "Palnadu"
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted form data
    $district = $_POST['district'];
    $city = $_POST['city'];

    require 'db.php';

    // Check if the city already exists in the database for the selected district
    $sql_check = $conn->prepare("SELECT COUNT(*) FROM Location WHERE district = ? AND city = ?");
    $sql_check->bind_param("ss", $district, $city);
    $sql_check->execute();
    $sql_check->bind_result($count);
    $sql_check->fetch();
    $sql_check->close();

    // If the city already exists for the district, display an error
    if ($count > 0) {
        $message = "Error: The city '$city' already exists in district '$district'.";
    } else {
        // Prepare the query to insert the data into the Location table
        $sql = $conn->prepare("INSERT INTO Location (district, city) VALUES (?, ?)");
        $sql->bind_param("ss", $district, $city);  // "ss" indicates the type of variables (both are strings)

        if ($sql->execute()) {
            $message = "Location saved successfully!";
        } else {
            $message = "Error saving location: " . $conn->error;
        }
        $sql->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #3498db, #2ecc71);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        label {
            font-size: 16px;
            color: #555;
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: 500;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
            outline: none;
        }

        select:focus, input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register District Locations</h2>

    <!-- Display success/error message -->
    <?php if (isset($message)) { ?>
        <div class="message <?php echo isset($error) ? 'error-message' : 'success-message'; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form action="" method="POST">
        <!-- District Select -->
        <label for="district">Select District:</label>
        <select name="district" id="district" required>
            <option value="">Choose District</option>
            <?php foreach ($ap_districts as $district) { ?>
                <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
            <?php } ?>
        </select>

        <!-- City Input -->
        <label for="city">Enter City:</label>
        <input type="text" name="city" id="city" placeholder="Enter city name" required>

        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
