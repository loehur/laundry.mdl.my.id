<?php
$array = array(0 => 'Setoran', 1 => 'NonTunai', 2 => 'HapusOrder', 3 => 'HapusDeposit', 4 => 'Pengeluaran')
?>

<div class="row mx-2 border-bottom pb-2">
    <?php
    $classActive = "";
    foreach ($array as $a) { ?>
        <div class="col-auto px-1" style="white-space: nowrap;">
            <?php $count = count($data[$a]);
            $classActive = ($a == $data['mode']) ? "bg-white" : "";
            ?>
            <a href="<?= URL::BASE_URL ?>AdminApproval/index/<?= $a ?>" class="border rounded pb-2 <?= $classActive ?>">
                <?php if ($count > 0) { ?>
                    <h6 class="m-0 btn"><?= $a ?> <span class="badge badge-danger"><?= $count ?></span></h6>
                <?php } else { ?>
                    <h6 class="m-0 btn"><?= $a ?> <i class="text-success fas fa-check-circle"></i></span></h6>
                <?php } ?>
            </a>
        </div>
    <?php }
    ?>
</div>

<div class="row mx-0">
    <div class="col px-2 pt-1" id="load" style="max-width: 740px;">
    </div>
</div>


<script>
    $(document).ready(function() {
        loadContent('<?= $data['mode'] ?>')
    });

    function loadContent(mode) {
        $(".loaderDiv").fadeIn("fast");
        $("div#load").load("<?= URL::BASE_URL ?>" + mode);
        $(".loaderDiv").fadeOut("slow");
    }
</script>