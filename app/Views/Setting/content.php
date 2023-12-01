<div class="content mt-1">
    <div class="container-fluid">
        <div class="card mr-2 ml-2">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col">
                        Set Harga
                    </div>
                    <div class="col">
                        <select class="form-select form-select-sm def_price" data-mode="def_price">
                            <option value="0" <?= ($this->mdl_setting['def_price'] == 0) ? "selected" : "" ?>>Set A</option>
                            <option value="1" <?= ($this->mdl_setting['def_price'] == 1) ? "selected" : "" ?>>Set B</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        Printer Margin Left
                    </div>
                    <div class="col-auto">
                        <span class="cell" data-mode="print_ms"><?= $this->mdl_setting['print_ms'] ?></span> mm
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col">
                        Salin Pengaturan Gaji
                    </div>
                </div>
                <form action="<?= $this->BASE_URL ?>Setting/salin_gaji" method="POST">
                    <div class="row mb-1">
                        <div class="col-md-1 mt-1"><label>Dari</label></div>
                        <div class="col">
                            <select name="sumber" class="select2 form-control form-control-sm" required>
                                <option value="" selected disabled></option>
                                <?php foreach ($this->user as $a) { ?>
                                    <option value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                                <?php } ?>
                                <?php if (count($this->userCabang) > 0) { ?>
                                    <?php foreach ($this->userCabang as $a) { ?>
                                        <option value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-1 mt-1"><label>Ke</label></div>
                        <div class="col">
                            <select name="target" class="select2 form-control form-control-sm" required>
                                <option value="" selected disabled></option>
                                <?php foreach ($this->user as $a) { ?>
                                    <option value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                                <?php } ?>
                                <?php if (count($this->userCabang) > 0) { ?>
                                    <?php foreach ($this->userCabang as $a) { ?>
                                        <option value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                                    <?php } ?>
                                <?php } ?>
                                <option value="0">00-ALL</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col">
                            <small class="text-danger"><i>Tidak termasuk Tunjangan</i></small>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-outline-secondary mt-1 float-end">Salin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.select2').select2();
    });

    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(res) {
                if (res.length == 0) {
                    alert("Salin Gaji Sukses!");
                    location.reload(true);
                } else {
                    alert(res);
                }
            },
        });
    });

    var click = 0;
    $(".cell").on('dblclick', function() {
        click = click + 1;
        if (click != 1) {
            return;
        }

        var value = $(this).html();
        var mode = $(this).attr('data-mode');
        var value_before = value;
        var span = $(this);

        var valHtml = $(this).html();
        span.html("<input type='number' min='0' id='value_' value='" + value + "'>");

        $("#value_").focus();
        $("#value_").focusout(function() {
            var value_after = $(this).val();
            if (value_after === value_before) {
                span.html(valHtml);
                click = 0;
            } else {
                $.ajax({
                    url: '<?= $this->BASE_URL ?>Setting/updateCell',
                    data: {
                        'value': value_after,
                        'mode': mode
                    },
                    type: 'POST',
                    dataType: 'html',
                    success: function(response) {
                        location.reload(true);
                    },
                });
            }
        });
    });

    $(".def_price").on('change', function() {
        var value = $(this).val();
        var mode = $(this).attr('data-mode');
        var value_before = "<?= $this->mdl_setting['def_price'] ?>";
        if (value === value_before) {
            return;
        } else {
            $.ajax({
                url: '<?= $this->BASE_URL ?>Setting/updateCell',
                data: {
                    'value': value,
                    'mode': mode
                },
                type: 'POST',
                dataType: 'html',
                success: function() {},
            });
        }
    });
</script>