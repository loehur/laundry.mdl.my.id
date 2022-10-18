<?php
$array = array(0 => 'Setoran', 1 => 'nonTunai', 2 => 'hapusOrder', 3 => 'hapusDeposit')
?>

<div class="content">
    <div class="container-fluid">
        <div class="row border-bottom pb-2" style="max-width: 747px;">
            <?php
            $classActive = "";
            foreach ($array as $a) { ?>
                <div class="col" style="white-space: nowrap;">
                    <?php $count = count($data[$a]);
                    $classActive = ($a == $data['mode']) ? "bg-white" : "";
                    ?>
                    <a href="<?= $this->BASE_URL ?>AdminApproval/index/<?= $a ?>" class="border rounded pb-2 <?= $classActive ?>">
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
        <div class="row">
            <div class="col pt-1" id="load">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadContent('<?= $data['mode'] ?>')
    });

    function loadContent(mode) {
        $(".loaderDiv").fadeIn("fast");
        $("div#load").load("<?= $this->BASE_URL ?>" + mode);
        $(".loaderDiv").fadeOut("slow");
    }
</script>