<link rel="stylesheet" type="text/css" href="<?= $this->ASSETS_URL ?>plugins/openstreet/leaflet.css">

<?php $log = $data['pelanggan'] ?>

<style>
    #map {
        width: 100%;
        height: 300px;
        border-radius: 5px;
    }
</style>

<form class="ajax" action="<?= URL::BASE_URL ?>Pelanggan_Lokasi/update/<?= $log['id_pelanggan'] ?>" method="POST">
    <div class="container">
        <div style="max-width: 500px;">
            <div class="row">
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <input id="latitude" class="form-control shadow-none alamat" name="lat" value="<?= isset($log['latt']) ? $log['latt'] : "" ?>" required />
                        <label for="latitude">Latitude</label>
                    </div>
                </div>
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <input id="longitude" class="form-control shadow-none alamat" name="long" value="<?= isset($log['longt']) ? $log['longt'] : "" ?>" required />
                        <label for="longitude">Longitude</label>
                    </div>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col px-1 mb-1">
                    <div id="map"></div>
                </div>
            </div>
            <div class="row">
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <input readonly type="text" class="form-control shadow-none" name="nama" required value="<?= strtoupper($log['nama_pelanggan']) ?>" id="floatingInput456">
                        <label for="floatingInput456">Nama</label>
                    </div>
                </div>
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <input readonly type="text" class="form-control shadow-none" <?= isset($log['hp']) ? "readonly" : "" ?> name="hp" required value="<?= $log['nomor_pelanggan'] ?>" id="floatingInput1654">
                        <label for="floatingInput1654">Nomor HP</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <input class="form-control shadow-none alamat" required name="alamat" value="<?= $log['alamat'] ?>" id="floatingTextarea" />
                        <label for="floatingTextarea">Jalan/No. Rumah/Dll</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col px-1 mb-1">
                    <div class="form-floating">
                        <select class="form-select shadow-none" id="kecamatan" name="kecamatan" required>
                            <option selected value=""></option>
                            <?php
                            foreach ($data['kec'] as $key => $dp) { ?>
                                <option value="<?= $key ?>"><?= str_replace("+", " ", $key) ?></option>
                            <?php } ?>
                        </select>
                        <label for="kecamatan">Kecamatan</label>
                    </div>
                </div>
                <div class="col px-1 mb-1" id="selKodePos">
                    <small class='text-secondary'>Kode Pos</small>
                </div>
            </div>
            <div class="row">
                <div class="col text-primary fw-bold">
                    <?= $log['area_name'] ?>
                </div>
            </div>
            <div class="row mt-1 border-top pt-2">
                <div class="col px-1 mb-1">
                    <button type="submit" class="btn btn-success w-100">Simpan Lokasi</button>
                </div>
            </div>
        </div>
    </div>
</form>


<script src="<?= $this->ASSETS_URL ?>plugins/openstreet/leaflet.js"></script>
<script>
    var glat = <?= $data['geo']['lat'] ?>;
    var glong = <?= $data['geo']['long'] ?>;


    $(document).ready(function() {
        let mapOptions = {
            center: [glat, glong],
            zoom: 15
        }
        let map = new L.map('map', mapOptions);
        let layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
        let marker = null;
        map.addLayer(layer);

        if (marker !== null) {
            map.removeLayer(marker);
        }
        marker = L.marker([glat, glong]).addTo(map);

        document.getElementById('latitude').value = glat;
        document.getElementById('longitude').value = glong;
        $("div.leaflet-control-attribution").addClass("d-none");
        map.on('click', (event) => {
            if (marker !== null) {
                map.removeLayer(marker);
            }
            marker = L.marker([event.latlng.lat, event.latlng.lng]).addTo(map);
            document.getElementById('latitude').value = event.latlng.lat;
            document.getElementById('longitude').value = event.latlng.lng;
        })

    });

    $("#kecamatan").on("change", function() {
        var val = $(this).val()
        if (val != "") {
            $("#selKodePos").load("<?= URL::BASE_URL ?>Load/Spinner/1", function() {
                $(this).load("<?= URL::BASE_URL ?>Pelanggan_Lokasi/kode_pos", {
                    input: val,
                })
            })
        } else {
            $("#selKodePos").load("<?= URL::BASE_URL ?>Load/Spinner/1", function() {
                $("#selKodePos").html("<small class='text-secondary'>Kode Pos</small>")
            })
        }
    })

    $("form.ajax").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            dataType: 'html',
            success: function(res) {
                $("#contentLok").load("<?= URL::BASE_URL ?>Pelanggan_Lokasi/content/<?= $log['id_pelanggan'] ?>");
            },
        });
    })
</script>