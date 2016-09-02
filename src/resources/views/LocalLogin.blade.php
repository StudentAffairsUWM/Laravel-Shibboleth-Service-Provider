<html>
    <head>
        <title>Local Login</title>
        <style type="text/css">
            body {
                font-family: sans-serif;
            }
            form {
                color: grey;
            }
            div.login-box {
                margin: 10px auto;
                width: 100%;
                border: 1px solid grey;
                border-radius: 5px;
                padding: 10px;
                max-width: 400px;
                min-width: 300px;
            }
            .title {
                text-align: center;
                font-weight: 200;
                color: grey;
            }
            input[type="email"], input[type="password"] {
                width: 100%;
                padding: 5px;
                border-radius: 5px;
                border: 1px solid #cdcdcd;
            }
            input[type="submit"] {
                padding: 10px;
                border: 1px solid #cdcdcd;
                border-radius: 5px;
                background-color: #fff;
                min-width: 100%;
            }
            input[type="submit"]:hover {
                background-color: #cdcdcd;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2 class="title">Login to Continue</h2>
            <form action="/local" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                @if(Session::has('message'))
                  <p class="alert alert-info">
                    {{ Session::get('message') }}
                  </p>
                @else
                  <strong>Please login below</strong>
                @endif
                <p>
                    <label for="email">Username</label>
                    <input type="email" name="email" id="email" />
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" />
                </p>
                <p>
                    <input type="submit" value="Login">
                </p>
            </form>
        </div>
    </div>
</html>
