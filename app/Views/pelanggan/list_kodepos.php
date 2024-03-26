<div class="form-floating">
    <select class="form-select shadow-none" id="kodepos" name="kodepos" required>
        <option selected value=""></option>
        <?php
        foreach ($data as $dp) { ?>
            <option value="<?= $dp['id'] ?>"><?= $dp['postal_code'] ?></option>
        <?php } ?>
    </select>
    <label for="kota">Kode Pos</label>
</div>