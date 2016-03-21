<?php

namespace mitrii\attachments\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use mitrii\attachments\models\Attachment;
use mitrii\attachments\helpers\Image;
use mitrii\attachments\assets\DropzoneAsset;

class DropzoneWidget extends \yii\base\Widget
{
    /**
     * @var string The name of the file field
     */
    public $name = false;

    /**
     * @var \yii\base\Model The model for the file field
     */
    public $model = false;

    /**
     * @var string The attribute of the model
     */
    public $attribute = false;

    /**
     * @var array An array of options that are supported by Dropzone
     */
    public $options = array();

    protected $dictOptions = array(
        // The text used before any files are dropped
        'dictDefaultMessage' => "Перетащите файл сюда для его загрузки",

        // The text that replaces the default message text it the browser is not supported
        'dictFallbackMessage' => "Выш браузер не поддерживает перетаскивание для загрузки файла",

        // The text that will be added before the fallback form
        // If null, no text will be added at all.
        'dictFallbackText' => null,

        // If the filesize is too big.
        'dictFileTooBig' => "Файл слишком большой ({{filesize}}MiB). Максимальный размер: {{maxFilesize}}MiB.",

        // If the file doesn't match the file type.
        'dictInvalidFileType' => "Файлы этого типа нельзя загружать",

        // If the server response was invalid.
        'dictResponseError' => "Сервер ответил с кодом {{statusCode}}.",

        // If used, the text to be used for the cancel upload link.
        'dictCancelUpload' => "Отменить загрузку",

        // If used, the text to be used for confirmation when cancelling upload.
        'dictCancelUploadConfirmation' => "Вы хотите отменить загрузку файла?",

        // If used, the text to be used to remove a file.
        'dictRemoveFile' => "Удалить",

        // If this is not null, then the user will be prompted before removing a file.
        'dictRemoveFileConfirmation' => null,

        // Displayed when the maxFiles have been exceeded
        // You can use {{maxFiles}} here, which will be replaced by the option.
        'dictMaxFilesExceeded' => "Вы не можете загружать большее количество файлов",
    );


    /**
     * @var string The URL that handles the file upload
     */
    public $url = false;

    /**
     * @var array An array of supported MIME types
     */
    public $acceptedFiles = array();

    /**
     * @var array Javascipt functions attached to dropzone events
     */
    public $events = array();

    /**
     * @var array Attachments objects
     */
    public $files = array();

    public $show_previews_in_dropzone = false;

    public $maxFiles = null;

    /**
     * Create a div and the appropriate Javascript to make the div into the file upload area
     */
    public function run() {

        echo Html::tag('div', '', ['class' => 'dropzone', 'id' => $this->getId()]);

        if (!$this->show_previews_in_dropzone) echo Html::tag('div', '', ['id'=>$this->getId().'-previews', 'class' => 'dropzone-previews']);

        if (!$this->name && ($this->model && $this->attribute) && $this->model instanceof \yii\base\Model)
            $this->name = Html::getInputName($this->model, $this->attribute);

        if (!$this->url || $this->url == '')
            $this->url = Url::to(['/attachment/upload', 'name'=>$this->name]);

        $events = '';
        foreach ($this->events as $name => $event)
        {
            $events .= "this.on('{$name}',{$event});";
        }

        $files = '';
        foreach ($this->files as $attachment)
        {
            $size = (empty($attachment->size)) ? 0 : $attachment->size;
            $files .= "var mockFile = {upload: {progress: 100, bytesSent: {$size}, total: {$size}}, processing: true, status: 'success', accepted: true, name: '{$attachment->original_name}', size: {$size} }; this.emit('addedfile', mockFile);";


            if (strpos($attachment->type, 'image') === 0)
            {
                $thumbnail = Image::url($attachment->hash, empty($this->options['thumbnailWidth'])?100:$this->options['thumbnailWidth'], empty($this->options['thumbnailHeight'])?100:$this->options['thumbnailHeight']);
                $files .= "this.emit('thumbnail', mockFile, '{$thumbnail}');";
            }

            $files .= "  $(mockFile).data('hash', '{$attachment->hash}'); this.files.push(mockFile); ";
        }

        $this->options = ArrayHelper::merge($this->dictOptions, $this->options);

        $options = ArrayHelper::merge([
            'url' => $this->url,
            'parallelUploads' => true,
            'paramName' => $this->name,
            'acceptedFiles' => implode(',', $this->acceptedFiles),
            'addRemoveLinks' => true,
            'previewsContainer' => ($this->show_previews_in_dropzone) ? null : '#'.$this->getId().'-previews',
            //'accept' => "js:function(file, done){}",
            'init' => new JsExpression("function(){{$events} {$files}}")
        ], $this->options);

        $options = Json::encode($options);

        $script = "Dropzone.options.{$this->getId()} = {$options}";

        DropzoneAsset::register($this->view);

        $this->view->registerJs($script, View::POS_END, __CLASS__ . '#' . $this->getId());
    }
} 