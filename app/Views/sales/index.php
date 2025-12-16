<style>
  table {
    border-radius: 15px;
    overflow: hidden
  }
</style>

<div class="position-fixed w-100 bg-light mx-1" style="z-index:1000;top:0px;height:205px">
</div>
<div class="w-100 sticky-top px-1 mb-2" style="top:72px;z-index:1001">
  <div class="bg-white p-2 rounded border mb-2">
    <div class="row mx-0">
      <div class="col">
        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sales Order</h5>
        <small class="text-muted">Penjualan Barang</small>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid px-2">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <p class="text-muted text-center py-5">
            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
            Klik tombol <strong>Order</strong> untuk membuat order baru
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Action Button - Order -->
<button id="btnSalesOrder" class="btn btn-warning bg-gradient rounded-3 shadow-lg position-fixed d-flex align-items-center gap-2 px-3 py-2" 
   type="button" style="bottom: 24px; right: 24px; z-index: 1050;">
  <i class="fas fa-shopping-cart fa-lg"></i>
  <span class="fw-bold fs-6">Order</span>
</button>

<!-- Offcanvas Sales Order -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSalesOrder" aria-labelledby="offcanvasSalesOrderLabel" data-bs-backdrop="false" data-bs-scroll="true">
  <div class="offcanvas-header bg-warning bg-gradient">
    <h5 class="offcanvas-title fw-bold text-dark" id="offcanvasSalesOrderLabel"><i class="fas fa-shopping-cart me-2"></i>Buat Sales Order</h5>
    <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-3" id="salesOrderContent">
    <div class="d-flex justify-content-center align-items-center py-5">
      <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
</div>

<script src="<?= URL::EX_ASSETS ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= URL::EX_ASSETS ?>plugins/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>

<script>
  var formLoaded = false;
  var offcanvasSalesOrderEl = document.getElementById('offcanvasSalesOrder');
  
  if (offcanvasSalesOrderEl) {
      var bsOffcanvas = new bootstrap.Offcanvas(offcanvasSalesOrderEl);
      
      $('#btnSalesOrder').on('click', function() {
          bsOffcanvas.toggle();
      });
      
      // Load form when offcanvas opens
      offcanvasSalesOrderEl.addEventListener('show.bs.offcanvas', function () {
          if(!formLoaded) {
              $('#salesOrderContent').load('<?= URL::BASE_URL ?>Sales/form', function(response, status, xhr) {
                  if (status == "error") {
                      $('#salesOrderContent').html('<div class="alert alert-danger">Gagal memuat form: ' + xhr.status + " " + xhr.statusText + '</div>');
                  } else {
                      formLoaded = true;
                  }
              });
          }
      });
  }
</script>
