<?php
/**
 * Eliza - Simple php acceptance testing framework
 *
 *
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 *
 * @version		0.2.0
 * @date		April 18, 2013
 *
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

require_once(VENDORS_PATH.'aperiplus/algorithms.php');
require_once(VENDORS_PATH.'aperiplus/reflection.php');

class UndecoratedException extends Exception {}
class DecoratorMethodConflict extends Exception {}
class NoDecoratorSpecified extends Exception {}

class DecoratorStack {

    var $unknown_message_type = 'cannot find a decorator with method [%s]';
    var $method_conflict_message = "method conflict: class [%s] and class [%s] both implement [%s]";
    var $not_an_object_message = '[%s] passed to decorator stack';
    var $_decorators = array();
    var $method_conflicts = array();

    function __construct($decoratable, $ignore = array()) {
        $this->_decoratable = $decoratable;
        $this->_ignore = $ignore;
    }
    function setInjectorUsedByMixin(Phemto $injector) {
        $this->_injector = $injector;
    }
    function __call($method, $args = array()) {
        if(in_array($method, $this->_ignore)) {
            throw new UndecoratedException(sprintf(
                $this->unknown_message_type,
                $method));
        }
        $cufa_hack = array();
        foreach($args as $k => &$v) {
            $cufa_hack[$k] =& $v;
        }

        return call_user_func_array(
            array($this->_getDecoratorWith($method), $method),
            $cufa_hack);
    }
    function _getDecoratorWith($method) {
        foreach($this->_decorators as $decorator) {
            if(method_exists($decorator, $method)) {
                return $decorator;
            }
        }
        throw new UndecoratedException(sprintf(
            $this->unknown_message_type,
            $method));
    }
    function mixin() {
        $args = func_get_args();
        if("" === $args[0]) {
            throw new NoDecoratorSpecified;
        }
        $decorator = call_user_func_array(
            array($this, '_getInstanceOf'),
            $args);
        $this->_decorators[] = $decorator;
        if($this->_haveMethodConflict()) {
            throw new DecoratorMethodConflict(
                $this->_getMethodConflicts());
        }
        return $decorator;
    }
    function _injector() {
        if( !isset($this->_injector)) {
            require_once(VENDORS_PATH.'phemto/phemto.php');
            $this->_injector = new Phemto;
        }
        return $this->_injector;
    }
    #!! temporary hack (AperiTestcase::addNonDecoratingFixture)
    #   this is really injector logic - subclass phemto?
    function getInstanceOf() {
        $func_args = func_get_args();
        return call_user_func_array(
            array($this, '_getInstanceOf'),
            $func_args);
    }
    function _getInstanceOf() {
        $func_args = func_get_args();
        $could_be_anything_really = array_shift($func_args);
        if(is_object($could_be_anything_really)) {
            return $could_be_anything_really;
        }
        #!! any issues with this..? willTemporarilyUse ??
        if($this->_shouldUse($could_be_anything_really)) {
            $this->_injector()->willUse($could_be_anything_really);
        }
        array_unshift($func_args, $could_be_anything_really);
        return call_user_func_array(
            array($this->_injector(), 'create'),
            $func_args);
    }
    function _shouldUse($class) {
        $introspector = new ReflectionClass($class);
        return ( !$introspector->isAbstract()
            and !$introspector->isInterface());
    }
    /*  Do >1 decorators implement the same method?
    */
    function _haveMethodConflict() {
        $this->method_conflicts = array();
        new CartesianSelfProduct(
            $this,
            '_checkForConflicts',
            $this->getDecoratorNames());
        return (bool)count($this->method_conflicts);
    }
    function _checkForConflicts($first, $second) {
        $conflicts = array_diff(
            array_intersect(
                $this->_getPublicMethods($first),
                $this->_getPublicMethods($second)),
            $this->_ignore);

        if(count($conflicts)) {
            $this->method_conflicts[] = sprintf(
                $this->method_conflict_message,
                $first,
                $second,
                implode(', ', $conflicts));
        }
    }
    function _getMethodConflicts() {
        return implode(' ', $this->method_conflicts);
    }
    /*  underscores = not public
    */
    function _getPublicMethods($class) {
        $methods = get_class_methods($class);
        foreach($methods as $key=>$method) {
            if(preg_match('/^_/', $method)) {
                unset($methods[$key]);
            }
        }
        return $methods;
    }
    function getDecoratorNames() {
        $names = array();
        foreach($this->_decorators as $decorator) {
            $names[] = get_class($decorator);
        }
        $names[] = get_class($this);
        return $names;
    }
    function whoImplements($method) {
        if(in_array($method, $this->_ignore)) {
            return sprintf($this->unknown_message_type, $method);
        }
        $decoratable_class = get_class($this->_decoratable);
        if($implementor = $this->_searchInHierarchy($decoratable_class, $method)) {
            return $implementor;
        } elseif($implementor = $this->_searchInDecoratorStack($method)) {
            return $implementor;
        } else {
            return sprintf($this->unknown_message_type, $method);
        }
    }
    function _searchInHierarchy($class, $method) {
        return call_user_func_array(
            array(new ImplementorFinder($class, $method), 'getImplementor'),
            array());;
    }
    function _searchInDecoratorStack($method) {
        foreach($this->getDecoratorNames() as $class) {
            if(in_array($method, get_class_methods($class))) {
                return $this->_searchInHierarchy($class, $method);
            }
        }
        return false;
    }
    function getInterface() {
        $all_contributing_classes = $this->getDecoratorNames();
        array_push(
            $all_contributing_classes,
            get_class($this->_decoratable));
        $interface = array();
        foreach($all_contributing_classes as $class) {
            $interface = array_merge(
                $interface,
                call_user_func_array(
                    array(new InterfaceExplainer($class, $this->_ignore), 'getInterface'),
                    array()));
        }
        usort($interface, array(get_class($this), '_orderByMethod'));
        return $interface;
    }
    static function _orderByMethod($first, $second) {
        return strcmp($first[1], $second[1]);
    }
}