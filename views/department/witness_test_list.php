<?php
if(!empty($details)) {
//    echo "<pre>"; print_r($details); echo "</pre>";
    echo '<table class="table table-bordered table-hover ">
            <tr>
                <th>#</th>
                <th>Department</th>
                <th>TOCR</th>
                <th>Test</th>
                <th>SubTest</th>
                <th>Action</th>
            </tr>';
    $i=1;
    foreach($details as $value) {
        echo '<tr>
                <td>'.$i++.'</td>
                <td>'.$value['dept_name'].'</td>
                <td>'.$value['tocr_number'].'</td>
                <td>'.$value['test_name'].'</td>
                <td>'.$value['sub_test_name'].'</td>
                <td><span class="btn btn-block btn-warning btn-xs" onclick="witness_seen('.$value['id'].')"> Witness Seen</span></td>
              </tr>';
    }
}
?>