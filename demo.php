<meta charset="utf-8" />
<?php
error_reporting(E_ALL);

require_once('awr_process.php');

if (isset($_POST['text'])) {
    $verse = $_POST['text'];
    $verse2 = new awr_process($verse);
    $verse2->register_filter("filter_qalqala");
    $verse2->register_filter("filter_ghunna");
    $verse2->register_filter("filter_lqlab");
    $verse2->register_filter("filter_ikhfaa");
    $verse2->register_filter("filter_idgham");
    $verse2->register_filter("filter_idgham_without_ghunna");
    $verse2->register_filter("filter_maddah");
    $verse2->process();
    $verse2->reorder();
    $verse2->render();
} else {
    die("متنی وارد نشده است ");
}
?>
