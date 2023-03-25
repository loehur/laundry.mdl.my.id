<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MDL | Log in</title>

    <link rel="icon" href="<?= $this->ASSETS_URL ?>icon/logo.png">
    <script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
    <!--  Bootstrap -->
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-4.6/bootstrap.min.css">
    <!--  IonIcons -->
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/ionicons.min.css">
    <!-- Font Awesome Icons -->
    <link href="<?= $this->ASSETS_URL ?>plugins/fontawesome-free-5.15.4-web/css/all.css" rel="stylesheet">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/css/adminlte.min.css">

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Titillium Web',
                sans-serif;
        }
    </style>
</head>


<script>
    $(document).ready(function() {
        $("#info").hide();
        $("#spinner").hide();
        $("form").on("submit", function(e) {
            $("#spinner").show();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: $(this).attr("method"),

                success: function(response) {
                    if (response == 1) {
                        $("#spinner").hide();
                        location.reload(true);
                    } else {
                        $("#info").hide();
                        $("#info").html('<div class="alert alert-danger" role="alert">' + response + '</div>')
                        $("#info").fadeIn();
                        $("#spinner").hide();
                    }
                },
            });
        });
    });
</script>

<body class="login-page small" style="min-height: 496.781px;">
    <div class="login-box">
        <div class="login-logo">
            <a href="#">MDL | <span class="text-info">Laundry</span></a><br>
        </div>
        <!-- /.login-logo -->
        <div class="card border border-info">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Login Session Baru</p>
                <div id="info"></div>
                <form action="<?= $this->BASE_URL ?>Login/cek_login" method="post">
                    <div class="input-group mb-3">
                        <input type="text" name="HP" class="form-control" placeholder="Nomor Handphone" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span<i class="fas fa-mobile-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="PASS" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">

                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <div id="spinner" class="spinner-border text-primary col-auto" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- <p class="mb-1">
                    <a href="?= $this->BASE_URL ?>Register" class="text-center">Daftar Baru</a>
                    <a href="?= $this->BASE_URL ?>Register/reset_pass" class="text-center text-info float-right">Lupa Password</a>
                </p> -->
                <!-- <hr> -->
                <!-- <p class="text-center">
                    Technical Support<br>081268098300 / 085278114125 (Whatsapp Only)
                </p> -->
            </div>
        </div>
    </div>
</body>

</html>