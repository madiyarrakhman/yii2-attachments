<?php
namespace musan\attachments\components;

use yii\web\UploadedFile;


class AttachmentFile extends \yii\base\Component
{
    private $_save_path;
    private $_hash;
    private $_path_deep = false;
    private $_subderictories;

    /**
     * @var UploadedFile
     */
    private $_uploadedFile;

    /**
     * @param string $name
     * @return AttachmentFile
     */
    public static function getInstanceByName($name)
    {
        $class = get_called_class();
        $self = new $class();
        $self->_uploadedFile = UploadedFile::getInstanceByName($name);

        return $self;
    }

    /**
     * @param string $upload_dir Path to upload directory
     * @param int $path_deep Number of subdirectories for file
     * @return bool
     */
    public function saveByDeep($upload_dir, $path_deep)
    {
        return $this->getUploadedFile()->saveAs($upload_dir . $this->generateSavePath($upload_dir, $path_deep));
    }

    /**
     * @param string $upload_dir
     * @param int $path_deep Number of subdirectories for file
     * @return string
     */
    public function generateSavePath($upload_dir, $path_deep = 5, $extension = null)
    {
        if ($this->_path_deep === false) $this->_path_deep = $path_deep;

        $hash = $this->getHash();
        $path = '';
        for ($i=0; $i<$this->_path_deep; $i++) {
            $c = $hash[$i];
            $path .= $c.'/';
        }

        if (!file_exists($upload_dir . $path)) {
            mkdir($upload_dir . $path, 0775, true);
        }

        $this->_save_path = $path . substr($hash, $this->_path_deep) . '.' . strtolower((empty($extension) ? $this->getExtensionName() : $extension));
        return $this->_save_path;
    }

    /**
     * @return string "Random" generated hash
     */
    public function getHash()
    {
        return empty($this->_hash) ? $this->_hash = md5(uniqid()) : $this->_hash;
    }

    public function getSavePath()
    {
        return $this->_save_path;
    }

    /**
     * @return UploadedFile
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