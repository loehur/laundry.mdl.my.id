<div class="content mt-3">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Card Printer Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: 1px; font-size: 12px;">
                            <i class="fas fa-print me-2"></i>Printer Settings
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small text-muted">Margin Top</label>
                                <input type="number" class="form-control" id="marginTop" min="0" max="20" 
                                    value="<?= $this->mdl_setting['margin_printer_top'] ?? 0 ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Feed Lines</label>
                                <input type="number" class="form-control" id="feedLines" min="0" max="20" 
                                    value="<?= $this->mdl_setting['margin_printer_bottom'] ?? 0 ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-dark w-100" id="btnSavePrinter">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </div>
                        <div id="saveStatus" class="mt-2 text-center d-none">
                            <small class="text-success"><i class="fas fa-check-circle"></i> Tersimpan!</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Info -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-light">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted mb-3" style="letter-spacing: 1px; font-size: 12px;">
                            <i class="fas fa-info-circle me-2"></i>Informasi
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Margin Top:</strong> Jarak dari atas kertas sebelum mulai print (dalam baris)
                            </li>
                            <li class="mb-2">
                                <strong>Feed Lines:</strong> Jumlah baris kosong setelah selesai print (untuk memudahkan potong kertas)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Save Printer Settings
    $("#btnSavePrinter").on('click', function() {
        var btn = $(this);
        var originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
        
        $.ajax({
            url: '<?= URL::BASE_URL ?>Setting/updatePrinterMargins',
            data: { 
                'margin_top': $("#marginTop").val(), 
                'feed_lines': $("#feedLines").val() 
            },
            type: 'POST',
            dataType: 'json',
            success: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Tersimpan!');
                $('#saveStatus').removeClass('d-none');
                setTimeout(function() { 
                    btn.html(originalHtml); 
                    $('#saveStatus').addClass('d-none');
                }, 2000);
            },
            error: function() {
                btn.prop('disabled', false).html(originalHtml);
                alert('Gagal menyimpan. Silakan coba lagi.');
            }
        });
    });
</script>
