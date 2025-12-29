<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create User</title>
</head>
<body>

    <center style="margin-top:50px;">
        <form action="/api/user" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Name" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required><br><br>
            <button type="submit">Create User</button>
        </form>
    </center>
</body>
</html>
