<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>404 Error</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: black;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .center {
            text-align: center;
        }

        .center img {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="center">
        <img src="/404/Guru_meditation.gif">
    </div>
    <script async defer>
        document.querySelector(".center").addEventListener("click", function() {
            history.back();
        });
    </script>
</body>

</html>