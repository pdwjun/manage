<?php

namespace vova07\roles\traits;

use vova07\roles\Module;
use Yii;

/**
 * Class ModuleTrait
 * @package vova07\roles\traits
 * Implements `getModule` method, to receive current module instance.
 */
trait ModuleTrait
{
    /**
     * @var \vova07\roles\Module|null Module instance
     */
    private $_module;

    /**
     * @return \vova07\roles\Module|null Module instance
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $module = Module::getInstance();
            if ($module instanceof Module) {
                $this->_module = $module;
            } else {
                $this->_module = Yii::$app->getModule('roles');
            }
        }
        return $this->_module;
    }
}
