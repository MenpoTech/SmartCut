<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Chat Application in Yii2';

$js = <<<JS
$('#chat-form').submit(function() {
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            $("#txt-msg").val("");
        }
    });
    return false;
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY)
?>
<div class="site-index">

    <div class="body-content">
        <?= Html::beginForm(['/site/chat'], 'POST', [ 'id' => 'chat-form']) ?>

        <div class="row">
            <div class="col-md-3">
                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Yii2 Chat</h3>
                        <div class="box-tools pull-right">
                            <span data-toggle="tooltip" title="1 New Messages" class="badge bg-light-blue">1</span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                                <i class="fa fa-comments"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="direct-chat-messages" id="chat-div">

                            <!--<div class="direct-chat-msg">
                                <div class="direct-chat-info clearfix">
                                    <span class="direct-chat-name pull-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
                                </div>
                                <div class="direct-chat-text">
                                    Is this template really for free? That's unbelievable!
                                </div>
                            </div>

                            <div class="direct-chat-msg right">
                                <div class="direct-chat-info clearfix">
                                    <span class="direct-chat-name pull-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
                                </div>
                                <div class="direct-chat-text">
                                    You better believe it!
                                </div>
                            </div>-->
                        </div>

                        <div class="direct-chat-contacts">
                            <ul class="contacts-list">
                                <li>
                                    <a href="#">
                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                            Count Dracula
                                            <small class="contacts-list-date pull-right">2/28/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">How have you been? I was...</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="box-footer">
                        <form action="#" method="post">
                            <div class="input-group">
                                <input type="text" name="message" id="txt-msg" placeholder="Type Message ..." autofocus class="form-control">
                                    <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary btn-flat">Send</button>
                                    </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?= Html::endForm() ?>
    </div>
</div>
<?php
$ip = 'localhost';

$js =<<<JS
$( document ).ready(function() {
    var socket = io.connect('http://$ip:8890');
    socket.on('chat_triggered', function (data) {
        var message = JSON.parse(data);
        $( "#chat-div" ).append('<div class="direct-chat-msg right"> <div class="direct-chat-warning clearfix"><span class="direct-chat-name pull-right">'+message.name+'</span><span class="direct-chat-timestamp pull-left">'+message.time+'</span></div><div class="direct-chat-text">'+message.message+' </div></div>');
        $('#chat-div').scrollTop($('#chat-div')[0].scrollHeight);
    });
    });
JS;
$this->registerJs($js,\yii\web\View::POS_READY);
?>