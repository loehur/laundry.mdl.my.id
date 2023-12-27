<div class="content mt-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <form action="<?php $this->BASE_URL ?>Export/export" method="POST">
          <div class="input-group input-group-sm">
            <span class="input-group-text text-primary">Sales</span>
            <input name="month" type="month" min="2023-07" max="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
            <button type="submit" class="btn btn-primary">Export</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>

</script>