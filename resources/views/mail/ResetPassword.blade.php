<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        p {
            color: #666;
        }

        .reset-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .reset-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <h4>Hi {{$user->full_name}}</h4>
        <p>Welcome to shop again</p>
        <h2>Password Reset</h2>
        <p>Click the link below to reset your password.</p>
        <a href="{{$resetLink}}" class="reset-link">Reset Password</a>
    </div>
</body>

</html>
