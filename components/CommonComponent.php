<?php
namespace app\components;


use Yii;
use yii\base\Component;


class CommonComponent extends Component
{
    public function display_amount($amount, $digits = 2)
    {
        if ($amount) {
            return number_format($amount, $digits);
        } else {
            return '';
        }
    }

    public function in_words($amount)
    {
        $no = round($amount);
        $point = round($amount - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $amount = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($amount) {
                $plural = (($counter = count($str)) && $amount > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($amount < 21) ? $words[$amount] . " " . $digits[$counter] . $plural . " " . $hundred : $words[floor($amount / 10) * 10] . " " . $words[$amount % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }
        }
        $str = array_reverse($str);
        $result = implode('', $str);
//        $points = ($point) ? "." . $words[$point / 10] . " " . $words[$point = $point % 10] : '';
        $points = '';
        $result = $result . "Rupees ";
        if(!empty($points)) {
            $result = $result . " . " .$points." Paise ";
        }else {
            $result = $result." Only";
        }
        return ucwords($result);
    }

    public function convertDate($date, $format)
    {
        if (!empty($date)) {
            return date($format, strtotime($date));
        } else {
            return '';
        }
    }

    public function Paymodes()
    {
        return array('1' => 'Cash', '2' => 'DD', '4' => 'Credit Card');
    }

    public function RefundTypes()
    {
        return array('1' => 'Adjust Bill', '2' => 'Refund');
    }

    public function RefundTypes1()
    {
        return array('1' => 'Adjust Bill');
    }

    public function RefundTypes2()
    {
        return array('2' => 'Refund');
    }
    public function PatientTypes()
    {
        return array('IP' => 'In Patient','OP' => 'OP Patient','DP' => 'Discharged Patients');
    }

    public function OrderBy()
    {
        return array('1' => 'Patient Name', '2' => 'Ward Wise');
    }

    function DateConvertDB($date = '')
    {
        if (!empty($date))
            return date('Y-m-d', strtotime($date));
        else
            return '';
    }

    public function pr($param = '')
    {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }
    public function Genders()
    {
        return array('Male'=>'Male','Female'=>'Female','TransGender'=>'TransGender');
    }
    public function Status()
    {
        return array('Pending' => 'Pending', 'Completed' => 'Completed');
    }
    public function Stations()
    {
        return array('DENTAL' => 'DENTAL', 'ENT' => 'ENT', 'EYE' => 'EYE');
    }
    public function tokens()
    {
        return array('Break Fast' => 'Break Fast', 'Lunch' => 'Lunch', 'Ankle Brachial Index / Biothesiometry' =>'Ankle Brachial Index / Biothesiometry');
    }

    public function getTimeSlot()
    {
        $query = "select substring(time_slot,1,5) as app_time,time_slot from (
SELECT to_char(generate_series(
   '01-08-2012 09:00:00'::timestamp,
   '01-08-2012 17:59:00'::timestamp,
   '15 minutes'::interval),'HH:mi AM') as time_slot)as a";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $time_slot = array();
        foreach ($res as $key => $value) {
            $time_slot[$value['app_time']] = $value['time_slot'];
        }
        return $time_slot;
    }

    public function getStyles() {
        $arr = array('SheetTitleFormat' => array(
            'font' => array(
                'name'=>'Arial',
                'size'=>15,
                'color' => array('rgb' => '000080'),
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => 'center',
            ),
        ),

            'SheetTitleFormat2' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'color' => array('rgb' => '000080'),
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            ),

            'SheetTitleFormat3' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>12,
                    'color' => array('rgb' => '000080'),
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            ),

            'TitleFormat' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>10,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'C0C0C0',
                    ),
                    'endcolor' => array(
                        'argb' => 'C0C0C0',
                    ),
                ),
            ),

            'TitleFormatLeft' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>10,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'left',
                ),
                'borders' => array(
                    'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'C0C0C0',
                    ),
                    'endcolor' => array(
                        'argb' => 'C0C0C0',
                    ),
                ),
            ),

            'fillcellredcolor' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                ),
                'alignment' => array(
                    'horizontal' => 'right',
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FF0000',
                    ),
                    'endcolor' => array(
                        'argb' => 'FF0000',
                    ),
                ),
            ),

            'AnnexureLeft' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                ),
                'alignment' => array(
                    'horizontal' => 'left',
                ),
                /* 'borders' => array(
                     'outline' => array(
                         'style' => \PHPExcel_Style_Border::BORDER_THIN,
                     ),
                 ),*/
            ),

            'AnnexureRight' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                ),
                'alignment' => array(
                    'horizontal' => 'right',
                ),
                /*  'borders' => array(
                      'outline' => array(
                          'style' =>\PHPExcel_Style_Border::BORDER_THIN,
                      ),
                  ),*/
            ),

            'AnnexureLeftTitle' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'left',
                ),
            ),

            'RegularFormatRight' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'right',
                    'vertical'=>'right',
                ),
                'style'=> array(
                    'format'=>\PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                ),
            ),
            'RegularFormatCenter' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                    'vertical'=>'center',
                ),
            ),

            'RegularFormatRightRed' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'color' => array('rgb' => 'FF0000'),
//                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'right',
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            ),

            'RegularFormatLeft' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>9,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'left',
                ),
                'borders' => array(
                    'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            ),
            'RegularFormat3' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>11,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'left',
                ),
                'borders' => array(
                    /*'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),*/
                ),
            ),
            'RegularFormat4' => array(
                'font' => array(
                    'name'=>'Arial',
                    'size'=>11,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                )
            )
        );
        return $arr;
    }
}