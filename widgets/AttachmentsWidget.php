<?php

namespace musan\attachments\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;
use musan\attachments\models\Attachment;

class AttachmentsWidget extends \musan\attachments\widgets\DropzoneWidget
{
    public $name = 'attachments';

    public $show_previews_in_dropzone = false;
    public $removeUrl ;

    public $events = [
        'success' => 'function(file, answer){}',
    ];

    public function init()
    {

        $this->removeUrl = empty($this->removeUrl) ? Url::to('/attachment/upload/delete') : $this->removeUrl;


        $this->events['success'] = new JsExpression("
            function(file, answer) {
                $(file).data('uid', answer.uid);

                refresh_attachments(this.files, '{$this->name}');

            }
        ");

        $removedFile = new JsExpression("
        function(file){

                $.post('{$this->removeUrl}', $(file).data(), function(data){
                    file.previewElement.parentNode.removeChild(file.previewElement);

                }, 'json');

                refresh_attachments(this.files, '{$this->name}');
        }
        ");

        $this->events['removedfile'] = empty($this->events['removedfile']) ? $removedFile : $this->events['removedfile'];

        $script = new JsExpression("
        function refresh_attachments(files, hidden_field_id)
        {
                    var files_list = [];
                    files.forEach(function(file) {
                        files_list.push({uid: $(file).data('uid'), status: file.status});
                    });
                    $('#'+hidden_field_id).val(JSON.stringify(files_list));
        }
        ");

        $this->view->registerJs($script, View::POS_END, __CLASS__ . '#' . $this->getId());

        //parent::init();
    }

    public function run()
    {
        $files = array();
        foreach($this->files as $file)
        {
            $files[] = array('uid' => $file->uid, 'status' => 'success');
        }
        echo Html::hiddenInput('attachments', Json::encode($files), ['id' => $this->name]);


        return parent::run();
    }

} 