<?php
/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JordiLlonch\Bundle\CrudGeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;



class JordiLlonchCrudGenerator extends DoctrineCrudGenerator
{
    protected $formFilterGenerator;
    protected $container;
    

    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        $this->generateFormFilter($bundle, $entity, $metadata, $forceOverwrite);
        $this->generateMenuItem($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);
        $this->generatePermissions($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);
    }
    
    public function generateMenuItem($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite){
        
        $fs = new Filesystem();
        $routeNamePrefix = str_replace('/', '_', $routePrefix);
        if(!$fs->exists($bundle->getPath().'/Resources'))
                throw new \RuntimeException($bundle->getPath().'/Resources folder not found.');
        
        if(!$fs->exists($bundle->getPath().'/Resources/config'))
                throw new \RuntimeException($bundle->getPath().'/Resources/config folder not found.');
        
        if(!$fs->exists($bundle->getPath().'/Resources/config/services.yml'))
                throw new \RuntimeException($bundle->getPath().'/Resources/config/services.yml file not found.');
        
        $yaml = new Parser();
        $value = $yaml->parse(file_get_contents($bundle->getPath().'/Resources/config/services.yml'));
        
        if(!$forceOverwrite && isset($value['parameters']['app.'.$routePrefix.'_configure_menu_listener.class']))
                throw new \RuntimeException(sprintf('Service parameter class %s already exists','app.'.$routePrefix.'_configure_menu_listener.class'));
        
        if(!$forceOverwrite && isset($value['services']['app.'.$routePrefix.'_configure_menu_listener']))
                throw new \RuntimeException(sprintf('Service definition %s already exists','app.'.$routePrefix.'_configure_menu_listener'));
        
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'MenuListener';
        $dirPath         = $bundle->getPath().'/EventListener';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'MenuListener.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s menu class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);
          
        $this->renderFile('menu/MenuListener.php.twig', $this->classPath, array(
            'fields_data'      => $this->getFieldsDataFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'route_prefix'     => $routePrefix,
            'bundle'           => $bundle->getName(),
            'needWriteActions' => $needWriteActions,
            'form_class'       => $this->className,
            'form_filter_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
        
        if((isset($value['parameters']['app.'.$routePrefix.'_configure_menu_listener.class']) ||
            isset($value['parameters']['app.'.$routePrefix.'_configure_menu_listener'])    )
                && !$forceOverwrite){
        
            throw new \RuntimeException(sprintf('Unable to generate the %s menu servoce as it already exists under the %s file', $this->className, $this->classPath));
            
        } else {
            unset($value['parameters']['app.'.$routePrefix.'_configure_menu_listener.class']);
            unset($value['parameters']['app.'.$routePrefix.'_configure_menu_listener']);
            
            $value['parameters']['app.'.$routePrefix.'_configure_menu_listener.class'] = $bundle->getNamespace().'\EventListener'.implode('\\', $parts).'\\'.$entityClass.'MenuListener';
            $value['services']['app.'.$routePrefix.'_configure_menu_listener'] = array(
                    'class' => '%app.'.$routePrefix.'_configure_menu_listener.class%',
                    'tags' => array(
                        array(
                            'name'=> 'kernel.event_subscriber',
                            'event' => 'app.menu_configure',
                            'method' => 'onMenuConfigure'
                        )
                    ),
                    'arguments'=> array("@security.token_storage")
            );
            $dumper = new Dumper();
            file_put_contents($bundle->getPath().'/Resources/config/services.yml', $dumper->dump($value,2));
        }
        
    }
    
    public function generatePermissions($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite){
        
        $fs = new Filesystem();
        if(!$fs->exists($bundle->getPath().'/Resources'))
                throw new \RuntimeException($bundle->getPath().'/Resources folder not found.');
        
        if(!$fs->exists($bundle->getPath().'/Resources/config'))
                throw new \RuntimeException($bundle->getPath().'/Resources/config folder not found.');
        
        if(!$fs->exists($bundle->getPath().'/Resources/config/services.yml'))
                throw new \RuntimeException($bundle->getPath().'/Resources/config/services.yml file not found.');
        
        $yaml = new Parser();
        $value = $yaml->parse(file_get_contents($bundle->getPath().'/Resources/config/services.yml'));
        
        if(!$forceOverwrite && isset($value['parameters']['app.'.$routePrefix.'_configure_permissions_listener.class']))
                throw new \RuntimeException(sprintf('Service parameter class %s already exists','app.'.$routePrefix.'_configure_permissions_listener.class'));
        
        if(!$forceOverwrite && isset($value['services']['app.'.$routePrefix.'_configure_permissions_listener']))
                throw new \RuntimeException(sprintf('Service definition %s already exists','app.'.$routePrefix.'_configure_permissions_listener'));
        
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'PermissionsListener';
        $dirPath         = $bundle->getPath().'/EventListener';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'PermissionsListener.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s permissions class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);
          
        $this->renderFile('permissions/PermissionsListener.php.twig', $this->classPath, array(
            'fields_data'      => $this->getFieldsDataFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'route_prefix'     => $routePrefix,
            'bundle'           => $bundle->getName(),
            'needWriteActions' => $needWriteActions,
            'form_class'       => $this->className,
            'form_filter_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
        
        if((isset($value['parameters']['app.'.$routePrefix.'_configure_permissions_listener.class']) ||
            isset($value['parameters']['app.'.$routePrefix.'_configure_permissions_listener'])    )
                && !$forceOverwrite){
        
            throw new \RuntimeException(sprintf('Unable to generate the %s permissions service as it already exists under the %s file', $this->className, $this->classPath));
            
        } else {
            unset($value['parameters']['app.'.$routePrefix.'_configure_permissions_listener.class']);
            unset($value['parameters']['app.'.$routePrefix.'_configure_permissions_listener']);
            
            $value['parameters']['app.'.$routePrefix.'_configure_permissions_listener.class'] = $bundle->getNamespace().'\EventListener'.implode('\\', $parts).'\\'.$entityClass.'PermissionsListener';
            $value['services']['app.'.$routePrefix.'_configure_permissions_listener'] = array(
                    'class' => '%app.'.$routePrefix.'_configure_permissions_listener.class%',
                    'tags' => array(
                        array(
                            'name'=> 'kernel.event_subscriber',
                            'event' => 'core_user.permissions.tree',
                            'method' => 'addModulePermissions'
                        )
                    )
            );
            $dumper = new Dumper();
            file_put_contents($bundle->getPath().'/Resources/config/services.yml', $dumper->dump($value,2));
        }
        
    }

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param $forceOverwrite
     *
     * @throws \RuntimeException
     */
    public function generateFormFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'FilterType';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'FilterType.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            'fields_data'      => $this->getFieldsDataFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_filter_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$this->className),
        ));
    }

    public function getFilterType($dbType, $columnName)
    {
        switch ($dbType) {
            case 'boolean':
                return 'filter_choice';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'filter_date_range';
            case 'date':
                return 'filter_date_range';
                break;
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'filter_number_range';
                break;
            case 'string':
            case 'text':
                return 'filter_text';
                break;
            case 'time':
                return 'filter_text';
                break;
            case 'entity':
            case 'collection':
                return 'filter_entity';
                break;
            case 'array':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            case 'virtual':
                throw new \Exception('The dbType "'.$dbType.'" is only for list implemented (column "'.$columnName.'")');
                break;
            default:
                throw new \Exception('The dbType "'.$dbType.'" is not yet implemented (column "'.$columnName.'")');
                break;
        }
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsDataFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldsData = (array) $metadata->fieldMappings;

        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            $fieldsData[$fieldName]['fieldName'] = $fieldName;
            $fieldsData[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
        }

        return $fieldsData;
    }

}