<?php
/**
 * File Uploader.
 */

namespace App\Service;

use App\Entity\Article;
use App\Entity\Thumbnail;
use App\Repository\ThumbnailRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader.
 */
class FileUploader
{
    /**
     * Target directory.
     *
     * @var string
     */
    private $targetDirectory;

    /**
     * @var App\Repository\ThumbnailRepository
     */
    private $thumbnailRepository;

    /**
     * FileUploader constructor.
     *
     * @param string $targetDirectory Target director
     */
    public function __construct(string $targetDirectory, ThumbnailRepository $repository)
    {
        $this->targetDirectory = $targetDirectory;
        $this->thumbnailRepository = $repository;
    }

    /**
     * Upload file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file    File to upload
     * @param Article                                             $article Article entity
     *
     * @return string Filename of uploaded file
     */
    public function upload(UploadedFile $file, Article $article): string
    {
        $fileName = $this->generateFilename(
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            $file->guessClientExtension()
        );

        $thumbnail = new Thumbnail();
        $thumbnail->setFilename($fileName);
        $thumbnail->setArticle($article);

        $this->thumbnailRepository->save($thumbnail);

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $exception) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    /**
     * Getter for target directory.
     *
     * @return string Target directory
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    /**
     * Generates new filename.
     *
     * @param string $originalFilename Original filename
     * @param string $extension        File extension
     *
     * @return string New filename
     */
    private function generateFilename(string $originalFilename, string $extension): string
    {
//        $safeFilename = transliterator_transliterate(
//            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
//            $originalFilename
//        );

        return 'img-'.uniqid().'.'.$extension;
    }
}
