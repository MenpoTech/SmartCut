<?php
namespace app\controllers;

use app\models\MstControlNumbers;
use app\models\MstItems;
use app\models\MstTaxes;
use app\models\MstUoms;
use app\models\TrnBillDetails;
use app\models\TrnBillHeaders;
use app\models\TrnSiteConfigs;
use Dompdf\Dompdf;
use kartik\widgets\ActiveForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

class InvoiceController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new TrnBillHeaders();
        $detail = new TrnBillDetails();

        $model->bill_date = date('d-m-Y');
        $model->mst_customer_id = 1;
        $detail->mst_item_id = 1;

        $tax =array();
        $tax_details = MstTaxes::find()->where(['status'=>1])->orderBy('tax_name asc')->all();
        foreach($tax_details as $t) {
            $tax[$t['id']]['id'] =$t['id'];
            $tax[$t['id']]['tax_name'] =$t['tax_name'];
            $tax[$t['id']]['tax_percent'] =$t['tax_percent'];
        }

        $uom = ArrayHelper::map(MstUoms::find()->where(['status'=>1])->orderBy('uom_name asc')->all(),'id','uom_name');
        $gst = ArrayHelper::map($tax,'id','tax_name');
        return $this->render('index',['model'=>$model,'detail'=>$detail,'uom'=>$uom,'gst'=>$gst,'tax'=>$tax]);
    }

    public function actionGetItemDetails() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $details = array();
        if(Yii::$app->request->isAjax) {
            $item_id = $_POST['item_id'];
            $details = MstItems::find()->join('inner join','mst_uoms',"mst_items.mst_uom_id = mst_uoms.id")->select(['mst_uoms.uom_name','mst_items.mst_uom_id','mst_items.item_name','mst_items.amount','mst_items.part_no','mst_items.hsn_code'])->where(['mst_items.id'=>$item_id])->one();
        }
        return $details;
    }

    public function actionValidateInvoiceForm()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new TrnBillHeaders(Yii::$app->getRequest()->getBodyParams()['TrnBillHeaders']);
            if (!$model->validate()) {
                return ActiveForm::validate($model);
            }
        }
    }

    public function actionSaveInvoice() {
        if(Yii::$app->request->isPost) {
            $obj = new MstControlNumbers();
            $bill_no = $obj->getExtId('invoice_no',date('Ym'),'INV');
            $header = new TrnBillHeaders();
            $header->mst_customer_id = $_POST['TrnBillHeaders']['mst_customer_id'];
            $header->bill_date  = date('Y-m-d');
            $header->bill_amount = 0;
            $header->bill_no = (!empty($bill_no['number_next'])?(!empty($bill_no['prefix'])?$bill_no['prefix'].'/'.sprintf('%04d',$bill_no['number_next']):$bill_no['number_next']):'');
            if($header->save()) {
                foreach($_POST['TrnBillDetails']['mst_item_id'] as $id=>$mst_item_id) {
                    $post = $_POST['TrnBillDetails'];
                    $details = new TrnBillDetails();
                    $details->trn_bill_header_id = $header->id;
                    $details->mst_item_id   = $mst_item_id;
                    $details->item_name     = $post['item_name'][$id];
                    $details->part_no       = $post['part_no'][$id];
                    $details->hsn_code      = $post['hsn_code'][$id];
                    $details->qty           = $post['qty'][$id];
                    $details->uint_amount   = $post['unit_amount'][$id];
//                    $details->     = $post[''][$id];
                    $details->mst_tax_id     = $post['mst_tax_id'][$id];
                    $details->mst_uom_id     = $post['mst_uom_id'][$id];
                    $details->uom_name     = $post['uom_name'][$id];
                    $details->save(false);
                }
            }else {
                echo "<pre>"; print_r($header->getErrors()); echo "</pre>"; exit;
            }
            Yii::$app->session->setFlash('success','Invoice Generated Successfully');
            return $this->redirect(Url::to(['invoice/index']));
        }
    }

    public function actionPrintInvoice($id=6) {
        $address = TrnSiteConfigs::findone(['var_name'=>'address'])->var_value;
        $q = "
SELECT
	header.id
    ,header.bill_no
    ,header.bill_date
    ,header.bill_amount
    ,customer.customer_name
    ,customer.customer_address
    ,customer.city
    ,customer.gstin_no
    ,customer.state_code
FROM
	trn_bill_headers as header
    JOIN mst_customers as customer on (customer.id = header.mst_customer_id)
WHERE header.id = :id
";
        $header_details = Yii::$app->getDb()->createCommand($q,['id'=>$id])->queryOne();

        $detail_query = "
SELECT
	details.item_name
    ,details.part_no
    ,details.hsn_code
    ,details.qty
    ,details.uom_name
    ,details.uint_amount
    ,details.tax_amount
    ,details.net_amount
    ,tax.tax_name
    ,tax.tax_percent
FROM
	trn_bill_headers as header
    JOIN trn_bill_details as details on (details.trn_bill_header_id = header.id)
    JOIN mst_customers as customer on (customer.id = header.mst_customer_id)
    JOIN mst_taxes as tax on (tax.id = details.mst_tax_id)
WHERE header.id =:id
ORDER BY details.id ASC
";
        $detail = Yii::$app->getDb()->createCommand($detail_query,['id'=>$id])->queryAll();

        $html = $this->renderPartial('print-invoice',['address'=>$address,'header_details'=>$header_details,'details'=>$detail]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        if(!empty($html)) {
            $title = $header_details['bill_no'].".pdf";
            $dompdf->stream($title, array('Attachment' => false));
            $pdf = $dompdf->output();
            echo $pdf; exit;
        }else {
            echo "No Records found";
        }
    }


}
