<?php
namespace app\controllers;
use app\models\TrnTestDetails;
use Dompdf\Dompdf;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ReportController extends \yii\web\Controller
{

    public function beforeAction() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(array('site/login'));
        }else {
            return true;
        }
    }

    public function actionIndex($mode='')
    {
        if($mode=='today') {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
        }elseif($mode=='week') {
            $day = date('w');
            $from_date  = date('Y-m-d', strtotime('-'.$day.' days'));
            $to_date    = date('Y-m-d', strtotime('+'.(6-$day).' days'));
        }elseif($mode=='month') {
            $from_date  = date('Y-m-01');
            $to_date    = date('Y-m-t');
        }elseif($mode=='year') {
            $from_date  = date('Y-m-d', strtotime('01-01-'.date('Y')));
            $to_date    = date('Y-m-d', strtotime('31-12-'.date('Y')));
        }else {
            $from_date = date('Y-m-d');
            $to_date   = date('Y-m-d');
        }

        return $this->render('index',['mode'=>$mode,'from_date'=>$from_date,'to_date'=>$to_date]);
    }

    public function actionGetDetails() {
        if(Yii::$app->request->isAjax) {
            $res = array();
            $from_date = date('Y-m-d', strtotime($_POST['from_date']));
            $to_date = date('Y-m-d', strtotime($_POST['to_date']));

            $from_date_string = date('d-M-Y', strtotime($_POST['from_date']));
            $to_date_string = date('d-M-Y', strtotime($_POST['to_date']));
            if ($_POST['type'] == 'detail') {

                $q = "select customer.customer_name,date(test.assign_date) as assign_date,test.* from trn_test_details as test JOIN mst_customers as customer on (customer.id=test.mst_customer_id) where  test.assign_date BETWEEN :from_date and :to_date ORDER BY date(test.assign_date) ASC,test.tocr_number ASC, test.test_name ASC ";
                $r = Yii::$app->getDb()->createCommand($q, ['from_date' => $from_date . ' 00:00:00', 'to_date' => $to_date . ' 23:59:59']);
                $query = $r->getRawSql();
                Yii::$app->session->set('report_query', $query);
                Yii::$app->session->set('report_type', 'detail');
                Yii::$app->session->set('report_title', ' TOCR Details from ' . $from_date_string . ' to ' . $to_date_string);
                $res = $r->queryAll();

                return $this->renderPartial('get-details', ['details' => $res]);
            } else {
                $q = "select distinct test.tocr_number,customer.customer_name,date(test.assign_date) as assign_date,COUNT(test.id) as no_of_test,'{Completed,Received}' &&  (array_agg(test.status)::text[])  as status from trn_test_details as test JOIN mst_customers as customer on (customer.id=test.mst_customer_id) where  test.assign_date BETWEEN :from_date and :to_date GROUP BY test.tocr_number,customer.customer_name,date(test.assign_date) ORDER BY date(test.assign_date) ASC,test.tocr_number ASC  ";
                $r = Yii::$app->getDb()->createCommand($q, ['from_date' => $from_date . ' 00:00:00', 'to_date' => $to_date . ' 23:59:59']);
                $query = $r->getRawSql();
                Yii::$app->session->set('report_query', $query);
                Yii::$app->session->set('report_type', 'summary');
                Yii::$app->session->set('report_title', ' TOCR Summary from ' . $from_date_string . ' to ' . $to_date_string);
                $res = $r->queryAll();

                return $this->renderPartial('get-summary', ['details' => $res]);
            }
        }
    }

    public function actionGeneratePdf() {
        $query = Yii::$app->session->get('report_query');
        if(!empty($query)) {
            $res = Yii::$app->getDb()->createCommand($query)->queryAll();
            $type = Yii::$app->session->get('report_type');
            if($type=='detail') {
                $html = $this->renderPartial('get-details',['details'=>$res]);
            }else {
                $html = $this->renderPartial('get-summary', ['details' => $res]);
            }
            $dompdf = new Dompdf();
            $dompdf->load_html($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            $dompdf->stream('tocr_detail');
        }
    }

    public function actionGenerateExcel() {
        $query  = \Yii::$app->session->get('report_query');
        $filename = 'Tocr.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$filename);
        $details = Yii::$app->getDb()->createCommand($query)->queryAll();
        if(!empty($details)) {
            $style = Yii::$app->common->getStyles();
            $spreadsheet = new \PHPExcel();
            $spreadsheet->setActiveSheetIndex(0);
            $obj = $spreadsheet->getActiveSheet();
            $row = 2;
            $col = 0;

            $hname = Yii::$app->params['company_name'];
            $address = Yii::$app->params['company_address'];
            $desc = Yii::$app->session->get('report_title');

            $obj->mergeCellsByColumnAndRow($col, $row, $col + 15, $row);
            $obj->setCellValueByColumnAndRow($col, $row, $hname);
            $obj->getStyleByColumnAndRow($col, $row++)->applyFromArray($style['SheetTitleFormat']);
            $obj->mergeCellsByColumnAndRow($col, $row, $col + 15, $row);
            $obj->setCellValueByColumnAndRow($col, $row, $address);
            $obj->getStyleByColumnAndRow($col, $row++)->applyFromArray($style['SheetTitleFormat2']);
            $obj->mergeCellsByColumnAndRow($col, $row, $col + 15, $row);
            $obj->setCellValueByColumnAndRow($col, $row, $desc);
            $obj->getStyleByColumnAndRow($col, $row++)->applyFromArray($style['SheetTitleFormat3']);

            $type = Yii::$app->session->get('report_type');
            if($type=='detail') {

                $obj->setCellValueByColumnAndRow($col++, $row, '#');
                $obj->setCellValueByColumnAndRow($col++, $row, 'TOCR Number');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Customer Name');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Test Date');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Test Name');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Sub Test Name');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Sample Details');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Heat No');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Sample ID');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Witness Date');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Remarks');
                $obj->setCellValueByColumnAndRow($col, $row, 'Status');

                $obj->getColumnDimension('A')->setAutoSize(true);
                $obj->getColumnDimension('B')->setAutoSize(true);
                $obj->getColumnDimension('C')->setAutoSize(true);
                $obj->getColumnDimension('D')->setAutoSize(true);
                $obj->getColumnDimension('E')->setAutoSize(true);
                $obj->getColumnDimension('F')->setAutoSize(true);
                $obj->getColumnDimension('G')->setAutoSize(true);
                $obj->getColumnDimension('H')->setAutoSize(true);
                $obj->getColumnDimension('I')->setAutoSize(true);

                $sno = 1;
                foreach ($details as $val) {
                    $row++;
                    $col = 0;
                    $obj->setCellValueByColumnAndRow($col++, $row, $sno++);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['tocr_number']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['customer_name']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['assign_date']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['test_name']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['sub_test_name']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['sample_details']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['heat_no']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['sample_id']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['witness_date']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['remarks']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['status']);
                }
            }else {
                $obj->setCellValueByColumnAndRow($col++, $row, '#');
                $obj->setCellValueByColumnAndRow($col++, $row, 'TOCR Number');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Customer Name');
                $obj->setCellValueByColumnAndRow($col++, $row, 'Test Date');
                $obj->setCellValueByColumnAndRow($col, $row, 'No.of Test');

                $obj->getColumnDimension('A')->setAutoSize(true);
                $obj->getColumnDimension('B')->setAutoSize(true);
                $obj->getColumnDimension('C')->setAutoSize(true);
                $obj->getColumnDimension('D')->setAutoSize(true);
                $obj->getColumnDimension('E')->setAutoSize(true);

                $sno = 1;
                foreach ($details as $val) {
                    $row++;
                    $col = 0;
                    $obj->setCellValueByColumnAndRow($col++, $row, $sno++);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['tocr_number']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['customer_name']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['assign_date']);
                    $obj->setCellValueByColumnAndRow($col++, $row, $val['no_of_test']);
                }
            }


            $writer = new \PHPExcel_Writer_Excel2007($spreadsheet);
            $writer->save('php://output');

        }
    }


}
