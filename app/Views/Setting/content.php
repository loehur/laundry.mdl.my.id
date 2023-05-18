<div class="content mt-1">
    <div class="container-fluid">
        <div class="card mr-2 ml-2">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        Printer Margin Left
                    </div>
                    <div class="col">
                        <span class="cell" data-mode="print_ms"><?= $this->mdl_setting['print_ms'] ?></span> mm
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>