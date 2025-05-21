<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #3498db, #2ecc71);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard {
            width: 90%;
            max-width: 1200px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
        }
        .main-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .card:hover {
            transform: scale(1.05);
            border: 2px solid #2ecc71;
        }
        .card h3 {
            font-size: 22px;
            color: #2c3e50;
        }
        .card p {
            font-size: 16px;
            color: #7f8c8d;
        }
        .card button {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .card button:hover {
            background: #27ae60;
        }
        .card a {
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .card {
                width: 45%;
            }
        }
        @media (max-width: 480px) {
            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Welcome to Your Admin Dashboard</h1>
        </div>
        <div class="main-content">
            <div class="card">
                <h3>Enter Data</h3>
                <p>Start adding new records to the database.</p>
                <a href="enter.php">
                    <button>Go to Enter Data</button>
                </a>
            </div>
            <div class="card">
                <h3>View Route</h3>
                <p>Review and manage existing Route data.</p>
                <a href="view.php">
                    <button>Go to View Data</button>
                </a>
            </div>
            <div class="card">
                <h3>Register District Locations</h3>
                <p>manage the District places information.</p>
                <a href="places.php">
                    <button>Manage Places</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
