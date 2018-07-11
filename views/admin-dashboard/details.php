<?php
?>
<div class="row">
    <div class="col-xs-12">
            <div class="box-body">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title bolder">
                            <i class="glyphicon glyphicon-list"></i> <?php echo $details['common']['tocr_number']; ?>
                        </h3>
                    </div>
                    <div class="table-responsive kv-grid-container">

                <table class="kv-grid-table table table-bordered table-striped table-condensed kv-table-wrap table-hover">
                    <thead class="table-header">
                    <tr>
                        <td>#</td>
                        <td>Test Name</td>
                        <td>Sub Test Name</td>
                        <td>Status</td>
                        <td>Assigned Date</td>
                        <td>Assigned By</td>
                        <td>Received Date</td>
                        <td>Received By</td>
                        <td>Completed Date</td>
                        <td>Completed By</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sno=1;
                    foreach($details['detail'] as $sample=>$dept) {
                        $sample_details = explode('~',$sample);
                        echo '<tr><td colspan="4" align="left"> Sample Details : '.$sample_details[0].'</td><td colspan="3" align="center"> Heat No : '.$sample_details[1].'</td> <td colspan="3" align="right"> Sample ID : '.$sample_details[2].'</td></tr>';
                        foreach($dept as $dept_name=>$list) {
                            echo '<tr><td colspan="10" align="center" class="bg-gray"> Department : '.$dept_name.'</td></tr>';
                            foreach($list as $val) {
                                echo '<tr>
                        <td>'.$sno++.'</td>
                        <td>'.$val['test_name'].'</td>
                        <td>'.$val['sub_test_name'].'</td>
                        <td>'.$val['status'].'</td>
                        <td>'.$val['assigned_date'].'</td>
                        <td>'.$val['assigned_by'].'</td>
                        <td>'.$val['received_date'].'</td>
                        <td>'.$val['received_by'].'</td>
                        <td>'.$val['completed_date'].'</td>
                        <td>'.$val['completed_by'].'</td>
                      </tr>';
                            }
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>