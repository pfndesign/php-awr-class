<meta charset="utf-8" />

<style>
   //style sample
.chunna {
    color: #FF6600;
}
.ikhfaa {
    color: #CC0000;
}
.qalqala {
    color: #00CC00;
}
.lqlab {
    color: #6699FF;
}
.idghamwg {
    color: #BBBBBB;
}
.idgham {
    color: #9900CC;
}
.maddah {
    color: #34495e;
}
</style>

<?php
error_reporting(E_ALL);

require_once('awr_process.php');

    $verse = "ﺗَﺮْﻣِﻴﻬِﻢْ ﺑِﺤِﺠَﺎﺭَﺓٍ ﻣِﻦْ ﺳِﺠِّﻴﻞٍ";
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

?>
