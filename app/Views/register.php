<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MDL | Register</title>
    <link rel="icon" href="<?= $this->ASSETS_URL ?>icon/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/ionicons.min.css">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/fontawesome-free-5.15.4-web/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-4.6/bootstrap.min.css">
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

<body class="login-page" style="min-height: 496.781px;">
    <div class="login-box">
        <div class="login-logo">
            <a href="#">MDL <b>Laundry</b></a>
        </div>
        <div class="card">
            <div class="card-body register-card-body small">
                <p class="login-box-msg">Register a new membership</p>

                <!-- ALERT -->
                <div id="info"></div>
                <form id="form" action="<?= URL::BASE_URL ?>Register/insert" method="post">
                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Singkat (Bukan Merek Usaha)" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" class="form-control" id="HP" name="HP" placeholder="Nomor HP" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <input type="password" class="form-control" id="repass" name="repass" placeholder="Ulangi Password" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <select id="kota" name="kota" class="form-control" required>
                                <option value="" disabled selected>Kota/Kab Domisili</option>
                                <?php foreach ($data['data_kota'] as $a) { ?>
                                    <option value="<?= $a['id_kota'] ?>"><?= $a['nama_kota'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                Register
                            </button>
                        </div>
                        <div id="spinner" class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </form>
                <a href="<?= URL::BASE_URL ?>Login" class="text-center">Sudah memiliki Akun dan LOGIN</a>
                <div class="error"><span></span></div>
            </div>
        </div>
    </div>

</body>

</html>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $("#info").fadeOut();
        $("#spinner").hide();

        $("form").on("submit", function(e) {
            $("#spinner").show();
            e.preventDefault();
            if ($('#password').val() != $('#repass').val()) {
                $("#info").hide();
                $("#info").fadeIn(1000);
                $("#info").html('<div class="alert alert-danger" role="alert">Konfirmasi Password tidak cocok!</div>')
                return;
            }
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: $(this).attr("method"),
                success: function(response) {
                    if (response == 0) {
                        $("#info").hide();
                        $('form').trigger("reset");
                        $("#info").fadeIn(1000);
                        $("#info").html('<div class="alert alert-success" role="alert">Registrasi Sukses! Akun sedang dalam proses Verifikasi</div>')
                        $("#spinner").hide();
                    } else {
                        $("#info").hide();
                        $("#info").fadeIn(1000);
                        $("#info").html('<div class="alert alert-danger" role="alert">' + response + '</div>')
                        $("#spinner").hide();
                    }
                },
            });
        });
    });
</script>