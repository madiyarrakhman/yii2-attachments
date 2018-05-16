<?php

namespace musan\attachments\widgets\imageuploader;

use yii\helpers\Html;
use yii\helpers\Json;
use \musan\attachments\helpers\Image;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * @deprecated
 */
class Widget extends InputWidget
{


    /**
     * @var array An array of options that are supported by Dropzone
     */
    public $options = array();

    public $removeUrl;

    public function getFilesList()
    {
        $files = array();
        foreach($this->model->attachments as $file)
        {
            $files[] = array('hash' => $file->hash, 'status' => 'success');
        }

        return Json::encode($files);
    }

    public function init()
    {
        parent::init();

        if (!$this->name && ($this->model && $this->attribute) && $this->model instanceof \yii\base\Model)
            $this->name = Html::getInputName($this->model, $this->attribute);

        //...

        $this->view->registerAssetBundle(DropzoneAsset::className());
        $this->view->registerAssetBundle(WidgetAsset::className());
    }


    public function run()
    {
        $this->removeUrl = empty($this->removeUrl) ? Url::to('/attachment/upload/delete') : $this->removeUrl;

        $previewTemplate = '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size" data-dz-size></div><img data-dz-thumbnail /></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-success-mark"><span>✔</span></div><div class="dz-error-mark"><span>✘</span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div>';

        echo Html::hiddenInput('attachments', $this->filesList, ['id' => "{$this->id}-input"]);

        echo Html::beginTag('div', ['id'=>$this->id, 'class' => 'dz dz-clickable', /*'style' => 'width:640px;height:480px;'*/]);
            echo Html::tag('div', '', ['class'=>'advert-pics dz-preview', 'id'=>"{$this->id}-container"]);
            echo '<div class="dz-message needsclick">Drop files here or click to upload.<br><span class="note needsclick">(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</span></div>';
        echo Html::endTag('div');
        ///echo ;



        $properties = Json::encode([
            'url' => Url::to(['/attachment/upload', 'name'=>$this->name]),
            'maxFilesize' => 10,
            'thumbnailWidth' => 320,
            'thumbnailHeight' => 240,
            'dictDefaultMessage' => '',
            'paramName' => $this->name,
            'addRemoveLinks' => true,
            'dictRemoveFile' => \Yii::t('app', 'Удалить'),
            // 'previewTemplate' => 
            'previewsContainer' => "#{$this->id}-container",

            'success' => new JsExpression("function(file, answer){
                $(file).data('hash', answer.hash);
                $(file).data('fileId', answer.id);
                refresh_attachments(this.files, '{$this->id}-input');
                }"),


            'removedfile' => new JsExpression("function(file){
                 $.post('{$this->removeUrl}', {'hash': file.hash}, function(data){
                    file.previewElement.parentNode.removeChild(file.previewElement);
                 }, 'json');
                    $.each(window.files_list, function(index, current_file){
                    if (current_file.fileId == $(file).data('fileId')){
                        window.files_list.splice(index, 1);
                        $('#{$this->id}-container div.dz-preview').eq(index).remove();
                        $('#' + '{$this->id}-input').val(JSON.stringify(window.files_list));
                        return false;
                    }
                });

            }"),
        ]);
    
        $this->view->registerJs("window.files_list = [];");
        //$this->view->registerJs("Dropzone.autoDiscover = false;", View::POS_HEAD);
        $this->view->registerJs("var myDropzone = new Dropzone('#{$this->id}', {$properties});");

        foreach ($this->model->attachments as $key=>$file) {
            $this->view->registerJs(new JsExpression("
                window.files_list.push({hash: '{$file->hash}', status: 'success', fileId: {$file->id}});

                var mockFile{$key} = {hash: '{$file->hash}', status: 'success', fileId: {$file->id},name: 'file{$key}', size: {$file->size}};
                $(mockFile{$key}).data('fileId', {$file->id});
                myDropzone.emit('addedfile', mockFile{$key});
                
                // And optionally show the thumbnail of the file:
                myDropzone.emit('thumbnail', mockFile{$key}, '".Image::url($file->hash, 200, 150)."');

                // Make sure that there is no progress bar, etc...
                myDropzone.emit('complete', mockFile{$key});
            "));   
        }
    }

} 