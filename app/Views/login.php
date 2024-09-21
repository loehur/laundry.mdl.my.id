<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MDL LAUNDRY</title>

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

                success: function(res) {
                    try {
                        data = JSON.parse(res);
                        if (data.code == 0) {
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-danger" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        } else if (data.code == 1) {
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-success" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        } else if ((data.code == 11)) {
                            location.reload(true);
                        } else if ((data.code == 10)) {
                            $("#captcha").attr('src', '<?= $this->BASE_URL ?>Login/captcha');
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-danger" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        }
                    } catch (e) {
                        $("#info").hide();
                        $("#info").html('<div class="alert alert-danger" role="alert">' + res + '</div>')
                        $("#info").fadeIn();
                        $("#spinner").hide();
                    }
                },
            });
        });

        $("#req_pin").on("click", function(e) {
            var hp_input = $('#hp').val();
            $("#spinner").show();
            e.preventDefault();
            $.ajax({
                url: '<?= $this->BASE_URL ?>Login/req_pin',
                data: {
                    hp: hp_input
                },
                type: 'POST',

                success: function(res) {
                    try {
                        data = JSON.parse(res);
                        if (data.code == 0) {
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-danger" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        } else if (data.code == 1) {
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-success" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        } else if ((data.code == 11)) {
                            location.reload(true);
                        } else if ((data.code == 10)) {
                            $("#captcha").attr('src', '<?= $this->BASE_URL ?>Login/captcha');
                            $("#info").hide();
                            $("#info").html('<div class="alert alert-danger" role="alert">' + data.msg + '</div>')
                            $("#info").fadeIn();
                            $("#spinner").hide();
                        }
                    } catch (e) {
                        $("#info").hide();
                        $("#info").html('<div class="alert alert-danger" role="alert">' + res + '</div>')
                        $("#info").fadeIn();
                        $("#spinner").hide();
                    }
                },
            });
        });

        $("span.freq_number").click(function() {
            $("input#hp").val($(this).html());
        })
    });
</script>

<body class="login-page small" style="min-height: 496.781px;">
    <div class="login-box">
        <div class="login-logo">
            <a href="#">MDL <span class="text-info">Login</span></a><br>
        </div>
        <!-- /.login-logo -->
        <div class="card border border-info rounded">
            <div class="card-body login-card-body rounded shadow">
                <?php if (count($data) > 0) { ?>
                    <p class="text-center">Choose frequently login number</p>
                    <p class="text-center">
                        <?php
                        krsort($data, 1);
                        foreach ($data as $ntm) { ?>
                            <span class="freq_number border rounded px-2 py-1" style="cursor: pointer"><?= $ntm ?></span>
                        <?php } ?>
                    </p>
                    <hr>
                <?php } ?>

                <div id="info"></div>
                <form action="<?= $this->BASE_URL ?>Login/cek_login" method="post">
                    <div class="input-group mb-3">
                        <input id="hp" type="text" name="username" class="form-control" autocomplete="username" placeholder="Nomor Whatsapp" required>
                        <div class="input-group-append">
                            <div class="input-group-text" id="req_pin" style="cursor: pointer;">
                                <span<i class="fas fa-mobile-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="pin" class="form-control" placeholder="PIN" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="cap" class="form-control" placeholder="Captcha" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <img id="captcha" src="<?= $this->BASE_URL ?>Login/captcha" alt="captcha" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">

                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <div id="spinner" class="spinner-border text-primary col-auto" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>