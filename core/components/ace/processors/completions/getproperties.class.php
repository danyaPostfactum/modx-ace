<?php
/**
 * @package modx
 * @subpackage processors.element
 */
class modCompletionsGetProperties extends modProcessor {
    public $object;
    
    public function checkPermissions() {
        return true;
    }
    public function getLanguageTopics() {
        return array('propertyset');
    }

    public function initialize() {
        $criteria = array();
        $criteria['name'] = $this->getProperty('key');
        $this->object = $this->modx->getObject($this->getProperty('classKey'),$criteria);
        return true;
    }

    public function process() {
        if (empty($this->object)) {
            return $this->toJSON(array());
        }
        $properties = $this->getObjectProperties();
        $list = array();
        if (!empty($properties) && is_array($properties)) {
            foreach ($properties as $key => $property) {
                $list[$key] = $property['desc_trans'];
            }
        }

        return $this->toJSON($list);
    }

    /**
     * Get the properties for the element
     * @return array
     */
    public function getObjectProperties() {
        $properties = $this->object->get('properties');
        $propertySet = $this->getProperty('propertySet');
        
        if (!empty($propertySet)) {
            /** @var modPropertySet $set */
            $set = $this->modx->getObject('modPropertySet',$propertySet);
            if ($set) {
                $setProperties = $set->get('properties');
                if (is_array($setProperties) && !empty($setProperties)) {
                    $properties = array_merge($properties,$setProperties);
                }
            }
        }
        return $properties;
    }

    /**
     * Prepare the property array for property insertion
     * 
     * @param string $key
     * @param array $property
     * @return array
     */
    public function prepareProperty($key,array $property) {
        $desc = $property['desc_trans'];
        if (!empty($property['lexicon'])) {
            $this->modx->lexicon->load($property['lexicon']);
        }

        if (is_array($property)) {
            $v = $property['value'];
            $xtype = $property['type'];
        } else { $v = $property; }

        $propertyArray = array(
            'key' => $key,
            'desc' => $desc
        );

        return $propertyArray;
    }

}
return 'modCompletionsGetProperties';