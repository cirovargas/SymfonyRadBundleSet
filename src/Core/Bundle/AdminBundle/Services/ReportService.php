<?php

namespace Core\Bundle\AdminBundle\Services;

use Behat\Transliterator\Transliterator;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
/**
 * Description of ListPaginationService
 *
 * @author ciro.marques
 */
class ReportService {
    
    const FORMAT_CSV = 'CSV';
    const FORMAT_XLS = 'XLS';
    const FORMAT_XML = 'XML';
    const FORMAT_PDF = 'PDF';
    
    private $phpexcel;
    private $templating;
    private $twig;
    private $snappy;
    
    public function __construct($phpexcel,$templating,$twig, $snappy){
        $this->phpexcel = $phpexcel;
        $this->templating = $templating;
        $this->twig = $twig;
        $this->snappy = $snappy;
    }
    
    public function generate(QueryBuilder $queryBuilder,$format,$labels = array(), $asResponse = false)
    {        
        if($format == self::FORMAT_XML){
            return $this->generateXML($queryBuilder,$labels, $asResponse);
        }
        
        if($format == self::FORMAT_CSV){
            return $this->generateCSV($queryBuilder,$labels, $asResponse);
        }
        
        if($format == self::FORMAT_XLS){
            return $this->generateXLS($queryBuilder,$labels, $asResponse);
        }
        
        if($format == self::FORMAT_PDF){
            return $this->generatePDF($queryBuilder,$labels, $asResponse);
        }
            
        throw new Exception('Invalid report format.');
    }
    
    private function generateXML($queryBuilder,$labels, $asResponse){
        
        $data = $this->processData($queryBuilder, $labels);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
        $xml .= '<itens xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";

        foreach($data['data'] as $line){
            $xml .= "<item>\n";
            $i = 0 ;
            foreach($line as $column){
                $xml .= '<'.Transliterator::urlize($data['header'][$i]).'>'.$column.'</'.Transliterator::urlize($data['header'][$i]).">\n";
                $i++;
            }
            $xml .= "</item>\n";
        }
        $xml .= "</itens>\n";

        $filename = $this->createFile($xml,self::FORMAT_XML);

        return $asResponse ? $this->createResponse($filename) : $filename;

    }
    
    private function generateCSV($queryBuilder,$labels, $asResponse){
        
        $data = $this->processData($queryBuilder, $labels);
        
        $content = implode(';', $data['header'])."\n";
        foreach($data['data'] as $l){
            $content .= implode(';', $l)."\n";
        }
        
        $filename = $this->createFile($content,self::FORMAT_CSV);

        return $asResponse ? $this->createResponse($filename) : $filename;
    }
    
    private function generateXLS($queryBuilder,$labels, $asResponse){
        
        
       $data = $this->processData($queryBuilder, $labels);
        
       $phpExcelObject = $this->phpexcel->createPHPExcelObject();
       
       array_unshift($data['data'],$data['header']);
       
       $phpExcelObject->setActiveSheetIndex(0);
       $phpExcelObject->getActiveSheet()->fromArray($data['data'], null, 'A1');
       $phpExcelObject->getActiveSheet()->setTitle('Relatorio');
       $phpExcelObject->setActiveSheetIndex(0);
       
       $filename = '/tmp/'.md5(uniqid()).'.xlsx';
       $writer = $this->phpexcel->createWriter($phpExcelObject);
       $writer->save($filename);
        
       return $asResponse ? $this->createResponse($filename) : $filename;
    }
    
    private function generatePDF($queryBuilder,$labels, $asResponse){

        $data = $this->processData($queryBuilder, $labels);

        $html = $this->renderView('CoreAdminBundle:Default:report.html.twig', array(
            'header' => $data['header'],
            'data' => $data['data']
        ));

        //$this->snappy->setOption('o','landscape');
        $filename = $this->createFile($this->snappy->getOutputFromHtml($html,array('orientation' =>'landscape')),self::FORMAT_PDF);

        return $asResponse ? $this->createResponse($filename) : $filename;
    }


    private function createFile($data,$extension){
        $filename = '/tmp/'.md5(uniqid()).'.'.strtolower($extension);
        $fp = fopen($filename, 'w');
        fwrite($fp,$data);
        fclose($fp);

        return $filename;
    }
    
    private function createResponse($filename){

        if(!file_exists($filename)){
            throw new \Exception('File not created.');
        }

        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;
    }
    
    private function processData($queryBuilder,$labels){
        $result = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
                
        return array(
            'header' => $this->createHeaderArray($result[0], $labels), 
            'data' => $this->createArray($result)
        );
    }
    
    private function createHeaderArray($line, $labels){
        $return = array();
        foreach($line as $key => $value){
            $return[] = isset($labels[$key])? $labels[$key] : $key;
        }
        
        return $return;
    }
    
    private function createArray($result){
        $return = array();
        
        foreach($result as $line){
            $l = array();
            foreach($line as $col){
                $l[] = $this->colToString($col);
            }
            $return[] = $l;
        }
        
        return $return;
    }
    
    private function colToString($col){
        if(!is_object($col))
            return (string)$col;
        
        if($col instanceof \DateTime)
            return $col->format('c');
        
        return $col->__toString();
    }


    private function renderView($view, array $parameters = array()){
        if ($this->templating) {
            return $this->templating->render($view, $parameters);
        }

        if (!$this->twig) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available.');
        }

        return $this->twig->render($view, $parameters);
    }
}