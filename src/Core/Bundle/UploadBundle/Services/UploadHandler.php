<?php
/**
 * Created by PhpStorm.
 * User: ciro
 * Date: 10/04/16
 * Time: 22:51
 */

namespace Core\Bundle\UploadBundle\Services;


use Core\Bundle\UploadBundle\Entity\Upload;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHandler
{

    public function handle($files){

        if(is_array($files)){
            $collection = new ArrayCollection();
            foreach($files as $file){

                if($file instanceof UploadedFile){
                    $collection->add($this->handlerFile($file));
                } else {
                    //throw new Exception('This is not a instance of Symfony\Component\HttpFoundation\File\UploadedFile');
                }

            }
            return $collection;
        }

        if($files instanceof UploadedFile){
            return $this->handlerFile($files);
        }

        return null;
        //throw new \Exception('Tipo de arquivo não identificado');

    }


    private function handlerFile($file){

        $upload = new Upload();
        $upload->setFile($file);

        return $upload;

    }

}