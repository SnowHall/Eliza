<?php
/*
    Note: these have been tested in situ in DecoratorStack
    tests.
*/

/*
    For each class in a hierarchy, find the public methods 
    defined in that class and pass them on to whoever it is 
    who might be interested in that sort of thing.
*/
abstract class HierarchyReflection {

    function __construct($top_level_class) {
        $this->_hierarchy = $this->_getHierarchy($top_level_class);
        $this->_process();
    }
    function _getHierarchy($top_level_class) {
        $hierarchy = class_parents($top_level_class);
        array_unshift($hierarchy, $top_level_class);
        $hierarchy = array_reverse($hierarchy);
        return $hierarchy;
    }
    function _process() {
        foreach($this->_hierarchy as $member) {
            $this->_action(
                $member, 
                $this->_getPublicMethodsDeclaredIn($member));
        }
    }
    /*  ignores underscored methods
    */
    function _getPublicMethodsDeclaredIn($class) {
        $in_given_class = array();
        $all_methods = call_user_func_array(
            array(new ReflectionClass($class), 'getMethods'), 
            array());
        foreach($all_methods as $method) {
            if($class == $method->getDeclaringClass()->getName()
                and !preg_match('/^_/', $method->getName())) {
                
                $in_given_class[] = $method->getName();
            }
        }
        return $in_given_class;
    }
    /*  abstract
    */
    function _action($class, $public_methods) {
    }
}
/*
    Find the highest class in hierarchy 
    implementing the given method.
*/
class ImplementorFinder extends HierarchyReflection {

    var $implementor = false;
    
    function __construct($top_level_class, $method) {
        $this->_method = $method;
        parent::__construct($top_level_class);
    }
    function _action($class, $public_methods) {
        if(in_array($this->_method, $public_methods)) {
            $this->implementor = $class;
        }
    }
    function getImplementor() {
        return $this->implementor;
    }
}
/*
    Find public methods and their declaring classes.
*/
class InterfaceExplainer extends HierarchyReflection {

    var $_interface = array();
    
    function __construct($top_level_class, $ignore = array()) {
        $this->_ignore = $ignore;
        parent::__construct($top_level_class);
    }
    function _action($class, $public_methods) {
        foreach($public_methods as $method) {
            if( !in_array($method, $this->_ignore)) {
                $this->_interface[] = array($class, $method);
            }
        }
    }
    function getInterface() {
        usort($this->_interface, array(get_class($this), '_orderByMethod'));
        return $this->_interface;
    }
    static function _orderByMethod($first, $second) {
        return strcmp($first[1], $second[1]);
    }
}