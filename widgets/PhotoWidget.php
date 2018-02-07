<?php

namespace musan\attachments\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use musan\attachments\models\Attachment;

class PhotoWidget extends \musan\attachments\widgets\DropzoneWidget
{
    public $show_previews_in_dropzone = true;
    public $removeUrl;
    public $value;

    public $events = array(
        'success' => 'function(file, answer){}',
    );

    public function init()
    {
        $this->value = $this->model->getAttribute($this->attribute);
        if (!empty($this->value))
        {
            $attachment = Attachment::findOne(['hash' => $this->value]);
            if (!empty($attachment))
            {
                $this->files[] = $attachment;
            }
        }

        $this->options['thumbnailWidth'] = empty($this->options['thumbnailWidth']) ? 325 : $this->options['thumbnailWidth'];
        $this->options['thumbnailHeight'] = empty($this->options['thumbnailHeight']) ? 240 : $this->options['thumbnailHeight'];

        $this->removeUrl = empty($this->removeUrl) ? Url::to(['/attachment/upload/delete']) : $this->removeUrl;

        $hidden_field_id = $this->getId() . '-input';

        $this->events['success'] = new JsExpression("
            function(file, answer) {
                $(file).data('hash', answer.hash);
                $('#{$hidden_field_id}').val(answer.hash);
                console.log(answer);
                console.log($('#{$hidden_field_id}').val());
            }
        ");

        $maxfilesexceeded = new JsExpression('function(file){this.removeFile(file);}');

        $removedFile = new JsExpression("
        function(file){
                $.post('{$this->removeUrl}', $(file).data(), function(data){
                    $('#{$hidden_field_id}').val('');
                    file.previewElement.parentNode.removeChild(file.previewElement);
                }, 'json');
        }
        ");

        $this->events['removedfile'] = empty($this->events['removedfile']) ? $removedFile : $this->events['removedfile'];
        $this->events['maxfilesexceeded'] = empty($this->events['maxfilesexceeded']) ? $maxfilesexceeded : $this->events['maxfilesexceeded'];

        parent::init();
    }

    public function run()
    {

        echo Html::activeHiddenInput($this->model, $this->attribute, ['id'=>$this->getId().'-input']);


        return parent::run();
    }
} 