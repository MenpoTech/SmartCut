<?php
use kartik\sortinput\SortableInput;
use yii\bootstrap\Html;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\Url;

$this->title ='Track TOCR Number';

echo Html::beginForm(['department/track'], 'post', ['data-pjax' => '', 'class' => 'form-inline', 'name' => 'track']);
$formatJs = <<< 'JS'
                var formatRepo = function (repo) {
                    if (repo.loading) {
                        return repo.tocr_number +' - '+repo.customer_name;
                    }
                    var markup = '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="col-sm-4"> <b>' + repo.tocr_number + '</b></div>' +
                    '<div class="col-sm-6">' + repo.customer_name + '</div>' +
                    '</div>';
                    return '<div style="overflow:hidden;">' + markup + '</div>';
                };
JS;

// Register the formatting script
$this->registerJs($formatJs, \yii\web\View::POS_HEAD);

$value = '';
$url = \yii\helpers\Url::to(['trn-customer/get-tocr-number']);
$value  = (!empty($tocr_number) ?$tocr_number:'');
echo "<center>".Select2::widget([
        'name' => 'tocr_number',
        'initValueText' => $value,
        'value' => (!empty($tocr_number)?$tocr_number:''),
        'size'=>'md',
        'options' => ['placeholder' => 'Select the TOCR Number', 'id' => 'tocr_number','onchange'=>'javascript:load_details(this.value);'],
        'pluginOptions' => [
            'allowClear' => false,
            'minimumInputLength' => 3,
            'width'=>'60%',
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('formatRepo'),
            'templateSelection' => new JsExpression('function (obj) { return obj.text;}'),
        ],
    ])."</center>";

if(!empty($details)) {
    $assigned = (!empty($details['assigned']) ? $details['assigned'] : 0);
    $received = (!empty($details['received']) ? $details['received'] : 0);
    $completed = (!empty($details['completed']) ? $details['completed'] : 0);
    $total = (!empty($details['total']) ? $details['total'] : 0);

    ?><br>
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>
                    <?php
                    $percent = round($completed / $total * 100,2);
                    ?>

                    <h3 class="box-title">Over all <?php echo $percent.'% Completed'?></h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="progress active">

                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" title="<?php echo $percent.'% Completed' ?>"
                             aria-valuenow="<?php echo $assigned; ?>" aria-valuemin="0"
                             aria-valuemax="<?php echo $percent; ?>" style="width: <?php echo $percent; ?>%">
                            <span class="sr-only"><?php echo $percent.'%' ?> Completed</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Donut Chart</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="donut-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Bar Chart</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="pieChart" style="height:auto"></canvas>
                    <table>
                        <tr>
                            <td style="background-color: #f56954">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp; &nbsp;&nbsp; Assigned&nbsp; </td>
                            <td style="background-color: #00a65a">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp; Received&nbsp;  </td>
                            <td style="background-color: #f39c12">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp; Completed&nbsp;  </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Bar Chart</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="bar-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title">Details</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="" style="height: auto;">
                        Total No of Test : <?php echo '<span class="bolder">'.$total.'</span>' ?>
                        Assigned Test    : <?php echo '<span class="bolder">'.$assigned.'</span>' ?>
                        Received Test    : <?php echo '<span class="bolder">'.$received.'</span>'?>
                        Completed Test   : <?php echo '<span class="bolder">'.$completed.'</span>'?>
                    </div>
                    <div>
                        <?php if(!empty($desc)) {
                            echo '<table class="table table-bordered table-stripped table-hover ">
                                    <thead class="table-header">
                                    <tr>
                                        <td>#</td>
                                        <td>Sample ID </td>
                                        <td>Sample Details</td>
                                        <td>Heat No</td>
                                        <td>Assigned Time</td>
                                        <td>Received Time</td>
                                        <td>Completed Time</td>
                                        <td>Witness Time</td>
                                    </tr>
                                    </thead>';
                            $sno=1;
                            foreach($desc as $value) {
                                echo '<tr>
                                        <td>'.$sno++.'</td>
                                        <td>'.$value['sample_id'].'</td>
                                        <td>'.$value['sample_details'].'</td>
                                        <td>'.$value['heat_no'].'</td>
                                        <td>'.$value['assign_date'].'</td>
                                        <td>'.$value['received_date'].'</td>
                                        <td>'.$value['completed_date'].'</td>
                                        <td>'.$value['witness_date'].'</td>
                                      </tr>';
                            }
                            echo '</table>';
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo Html::endForm();
    $js = <<<JS
var donutData = [
  {label: "Assigned", data: $assigned, color: "#3c8dbc"},
  {label: "Received", data: $received, color: "#0073b7"},
  {label: "Completed", data: $completed, color: "#00c0ef"}
];
$.plot("#donut-chart", donutData, {
  series: {
    pie: {
      show: true,
      radius: 1,
      innerRadius: 0.4,
      label: {
        show: true,
        radius: 2 / 3,
        formatter: labelFormatter,
        threshold: 0.1
      }

    }
  },
  legend: {
    show: false
  }
});

var bar_data = {
  data: [["Total", $total], ["Assigned", $assigned], ["Received", $received], ["Completed", $completed]],
  color: "#3c8dbc"
};
$.plot("#bar-chart", [bar_data], {
  grid: {
    borderWidth: 1,
    borderColor: "#f3f3f3",
    tickColor: "#f3f3f3"
  },
  series: {
    bars: {
      show: true,
      barWidth: 0.5,
      align: "center"
    }
  },
  xaxis: {
    mode: "categories",
    tickLength: 0
  }
});

function labelFormatter(label, series) {
return '<div style="font-size:13px; text-align:center; padding:1px; color: #fff; font-weight: 600;">'
        + label
        + "<br>"
        + Math.round(series.percent) + "%</div>";
}


var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas);
        var PieData = [
          {
            value: $assigned,
            color: "#f56954",
            highlight: "#f56954",
            label: "Assigned"
          },
          {
            value: $received,
            color: "#00a65a",
            highlight: "#00a65a",
            label: "Received"
          },
          {
            value: $completed,
            color: "#f39c12",
            highlight: "#f39c12",
            label: "Completed"
          }
        ];
        var pieOptions = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 5,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 0, // This is 0 for Pie charts
          //Number - Amount of animation steps
          animationSteps: 100,
          //String - Animation easing effect
          animationEasing: "easeOutBounce",
          //Boolean - Whether we animate the rotation of the Doughnut
          animateRotate: true,
          //Boolean - Whether we animate scaling the Doughnut from the centre
          animateScale: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true,
          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: false,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        pieChart.Doughnut(PieData, pieOptions);
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
//\dmstr\web\AdminLteCustomAsset::register($this);
    \dmstr\web\PieChartAsset::register($this);
}
?>
<br><br>
<?php echo Yii::$app->runAction('/tocr-new/index') ?>
<script>
    function load_details(val) {
        var url = '<?php echo Url::to(['department/track']) ?>&tocr_number=' + val;
        window.location = url;
    }
</script>