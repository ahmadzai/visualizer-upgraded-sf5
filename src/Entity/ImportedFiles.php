<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * District
 *
 * @ORM\Table(name="imported_files")
 * @ORM\Entity
 * @Vich\Uploadable
 *
 */
class ImportedFiles
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="imported_file", fileNameProperty="fileName", size="fileSize")
     *
     * @Assert\File(
     *     maxSize = "3M",
     *     maxSizeMessage = "Please upload a valid Excel file"
     * )
     * @var File
     */
    private $importedFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fileName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $fileSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $dataType;


    /**
     * @return NameSlug string
     */
    public function getNameSlug()
    {

        return $this->getDataType()."_".date("YmdHis");
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return File
     */
    public function getImportedFile()
    {
        return $this->importedFile;
    }

    /**
     * @param File|UploadedFile $importedFile
     *
     * @return ImportedFiles
     */
    public function setImportedFile(File $importedFile = null)
    {
        $this->importedFile = $importedFile;

        if ($importedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return ImportedFiles
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     * @return ImportedFiles
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

//    /**
//     * @Assert\Callback
//     * @param ExecutionContextInterface $context
//     */
//    public function validate(ExecutionContextInterface $context, $payload)
//    {
//
//        $ext = explode('.', $this->importedFile->g(), -1);
//
//        if (! in_array(strtolower(count($ext)>0?$ext[0]:null), array(
//            'xlsx',
//            'xls',
//            'csv'
//        ))) {
//            $context
//                ->buildViolation('Wrong file type (only xlsx,xls,csv are allowed)')
//                ->atPath('import_admin_data')
//                ->addViolation();
//        }
//    }





}
