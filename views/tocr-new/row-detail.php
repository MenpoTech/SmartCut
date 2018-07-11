<?php
use app\models\TrnTestDetails;
if(!empty($model)) {
    $q = "SELECT test.id,customer.customer_name, dept.dept_name,test.test_name,test.sub_test_name,test.tocr_number,test.sample_details,test.heat_no,test.sample_id,test.status,test.remarks, test.assign_date,assigned.displayname as assigned_by, received.displayname as received_by, test.received_date,completed.displayname as completed_by, test.completed_date from trn_test_details as test LEFT JOIN mst_users as assigned on (assigned.id=test.assigned_by) LEFT JOIN mst_users as received on (received.id=test.received_by) LEFT JOIN mst_users as completed on (completed.id=test.completed_by) JOIN mst_departments as dept on (dept.id=test.mst_department_id) JOIN mst_customers as customer on (customer.id=test.mst_customer_id) where test.tocr_number= :tocr_no ";
    $res = Yii::$app->getDb()->createCommand($q, ['tocr_no' => $model['tocr_number']])->queryAll();
    if (!empty($res)) {
        foreach ($res as $val) {
            $key = $val['sample_details'] . '~' . $val['heat_no'] . '~' . $val['sample_id'];
            $details['common']['tocr_number'] = $val['tocr_number'];
            $details['common']['customer_name'] = $val['customer_name'];
            $details['common']['remarks'] = $val['remarks'];

            $details['detail'][$key][$val['dept_name']][$val['id']]['test_name'] = $val['test_name'];
            $details['detail'][$key][$val['dept_name']][$val['id']]['sub_test_name'] = $val['sub_test_name'];
            $details['detail'][$key][$val['dept_name']][$val['id']]['status'] = $val['status'];
            $details['detail'][$key][$val['dept_name']][$val['id']]['assigned_date'] = (!empty($val['assign_date']) ? date('d-m-Y h:i A', strtotime($val['assign_date'])) : '');
            $details['detail'][$key][$val['dept_name']][$val['id']]['assigned_by'] = $val['assigned_by'];
            $details['detail'][$key][$val['dept_name']][$val['id']]['received_date'] = (!empty($val['received_date']) ? date('d-m-Y h:i A', strtotime($val['received_date'])) : '');
            $details['detail'][$key][$val['dept_name']][$val['id']]['received_by'] = $val['received_by'];
            $details['detail'][$key][$val['dept_name']][$val['id']]['completed_date'] = (!empty($val['completed_date']) ? date('d-m-Y h:i A', strtotime($val['completed_date'])) : '');
            $details['detail'][$key][$val['dept_name']][$val['id']]['completed_by'] = $val['completed_by'];
        }
        ?>
        <div data-key="1" data-index="0" class="skip-export kv-expanded-row kv-grid-demo" style="">
            <div class="kv-detail-content">
                <h3>
                    <span class="fa-1x bolder"><?= $model['tocr_number'] ?></span>
                    <?= $model['customer_name'] ?>
                    <span class="small text-muted"> &nbsp;&nbsp;&nbsp;&nbsp;Assigned On : <?= date('d-m-Y h:i A', strtotime($model['assign_date'])); ?></span>
                </h3>

                <div class="row">
                    <div class="col-sm-12">
                        <table class="small kv-grid-table table table-bordered table-striped table-condensed kv-table-wrap table-hover">
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
                                echo '
                                    <tr>
                                        <td colspan="4" align="left"> Sample Details : <span class="bolder">'.$sample_details[0].'</span></td>
                                        <td colspan="3" align="center"> Heat No : <span class="bolder">'.$sample_details[1].'</span></td>
                                        <td colspan="3" align="right"> Sample ID : <span class="bolder">'.$sample_details[2].'</span></td>
                                    </tr>';
                                foreach($dept as $dept_name=>$list) {
                                    echo '
                                    <tr>
                                        <td colspan="10" align="center" class="bg-gray"> Department : <span class="bolder"> '.$dept_name.'</span></td>
                                    </tr>';
                                    foreach($list as $val) {
                                        if ($val['status'] == 'Assigned') {
                                            $class = 'danger';
                                        } else if ($val['status'] == 'Received') {
                                            $class = 'warning';
                                        } else if ($val['status'] == 'Completed') {
                                            $class = 'success';
                                        } else {
                                            $class = '';
                                        }
                                        echo '<tr class="'.$class.'">
                                                <td>'.$sno++.'</td>
                                                <td>'.$val['test_name'].'</td>
                                                <td>'.$val['sub_test_name'].'</td>
                                                <td class="bolder">'.$val['status'].'</td>
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
        <?php
    }
}
?>