<?php
namespace musan\attachments\file;

use yii\web\UploadedFile as YiiUploadedFile;

/**
 * Class UploadedFile
 * @package musan\attachments\components\file
 */
class UploadedFile extends BaseFile
{

    private $_subderictories;

    /**
     * @var YiiUploadedFile
     */
    private $_uploadedFile;

    /**
     * @param string $name
     * @return UploadedFile
     */
    public static function getInstanceByName($name)
    {
        $class = get_called_class();
        $self = new $class();
        $self->_uploadedFile = YiiUploadedFile::getInstanceByName($name);

        return $self;
    }

    /**
     *
     * @return bool
     */
    public function save($deleteTempFile = false)
    {
        return $this->getUploadedFile()->saveAs($this->getFullSavePath());
    }

    /**
     * @return YiiUploadedFile
     */
    public function getUploadedFile()
    {
        return $this->_uploadedFile;
    }

    /**
     * @return string the original name of the file being uploaded
     */
    public function getName()
    {
        return $this->getUploadedFile()->name;
    }

    /**
     * @return string the path of the uploaded file on the server.
     * Note, this is a temporary file which will be automatically deleted by PHP
     * after the current request is processed.
     */
    public function getTempName()
    {
        return $this->getUploadedFile()->tempName;
    }

    /**
     * @return string the MIME-type of the uploaded file (such as "image/gif").
     * Since this MIME type is not checked on the server side, do not take this value for granted.
     * Instead, use {@link CFileHelper::getMimeType} to determine the exact MIME type.
     */
    public function getType()
    {
        return $this->getUploadedFile()->type;
    }

    /**
     * @return integer the actual size of the uploaded file in bytes
     */
    public function getSize()
    {
        return $this->getUploadedFile()->size;
    }

    /**
     * Returns an error code describing the status of this file uploading.
     * @return integer the error code
     * @see http://www.php.net/manual/en/features.file-upload.errors.php
     */
    public function getError()
    {
        return $this->getUploadedFile()->error;
    }

    /**
     * @return boolean whether there is an error with the uploaded file.
     * Check {@link error} for detailed error code information.
     */
    public function getHasError()
    {
        return $this->getUploadedFile()->getHasError();
    }

    /**
     * @return string the file extension name for {@link name}.
     * The extension name does not include the dot character. An empty string
     * is returned if {@link name} does not have an extension name.
     */
    public function getExtensionName()
    {
        return $this->getUploadedFile()->extension;
    }

}