<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/4/16
 * Time: 10:10 PM
 */

namespace App\Entity\Helpers;

use App\Exceptions\MethodNotExistingException;
use App\Exceptions\PropertyNotExistingException;

/**
 * Trait SubClassData
 * implements magic methods for getting and setting subclass data. Stores them in one field, which is stored via
 * doctrine as json_array / string (if json_array is not supported).
 * @package App\Entity
 */
trait SubClassData
{
//<editor-fold desc="Fields">
  /**
   * @var array
   * @ORM\Column(type="json_array")
   */
  private $subClassData;

//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * Magic function call for getters and setters of subclass data.
   * Triggers an error if the function was not a get or set method.
   * @param string $name function name
   * @param mixed[] $arguments the function arguments
   * @return $this|SubClassData|mixed either the value if it was a get request or $this if it was a set method
   * @throws MethodNotExistingException
   * @throws PropertyNotExistingException
   */
  public function __call(string $name, $arguments)
  {
    if (substr($name, 0, 3) === "get") {
      return $this->getProperty(substr($name, 3));
    } else if (substr($name, 0, 2) === "is") {
      return $this->getProperty(substr($name, 2));
    } else if (substr($name, 0, 3) === "set" && count($arguments) == 1) {
      return $this->setProperty(substr($name, 3), $arguments[0]);
    }
    throw new MethodNotExistingException(get_class($this), $name);
  }

  /**
   * Adds an subclass property if it is not already existent. The default value is used as value.
   * @param string $name the property name
   * @param mixed $default the default value for the property
   * @return $this|SubClassData
   */
  public function addPropertyIfNotExistent(string $name, $default)
  {
    if (!array_key_exists(strtolower($name), $this->subClassData)) {
      $this->subClassData[strtolower($name)] = $default;
    }
    return $this;
  }

  /**
   * Checks if the contains the given property as subclass data.
   * @param string $property the property name
   * @return bool true if it has the property and false otherwise
   */
  public function hasProperty(string $property): bool
  {
    return array_key_exists(strtolower($property), $this->subClassData) || property_exists($this, $property);
  }

  /**
   * Initializes the subclassData structure and adds the given keys with null values to it.
   * @param string[] $keys the keys of the subclass data properties (the names).
   * @return $this|SubClassData
   */
  public function initSubClassData(array $keys)
  {
    $this->subClassData = [];
    foreach ($keys as $key) {
      $this->subClassData[strtolower($key)] = null;
    }
    return $this;
  }

  /**
   * Checks if a method with the given name exists considering also getter and setter for subclass properties
   * @param string $method
   * @return bool
   */
  public function methodExists(string $method): bool
  {
    if (method_exists($this, $method)) {
      return true;
    }
    //check if method corresponds to existing property
    if (substr($method, 0, 3) === "get") {
      return $this->hasProperty(substr($method, 3));
    } else if (substr($method, 0, 2) === "is") {
      return $this->hasProperty(substr($method, 2));
    } else if (substr($method, 0, 3) === "set") {
      return $this->hasProperty(substr($method, 3));
    }

    return false;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets a property or throws an error if the property does not exist.
   * @param string $name the name of the property
   * @return mixed the value of the property
   * @throws PropertyNotExistingException
   */
  private function getProperty($name)
  {
    if (!array_key_exists(strtolower($name), $this->subClassData)) {
      throw new PropertyNotExistingException(get_class($this), strtolower($name), "getProperty");
    }
    return $this->subClassData[strtolower($name)];
  }

  /**
   * Sets a property with the given name and the given value
   * @param string $name the name of the property to set
   * @param mixed $value the new value for the property
   * @return $this|SubClassData
   * @throws PropertyNotExistingException
   */
  private function setProperty(string $name, $value)
  {
    if (!array_key_exists(strtolower($name), $this->subClassData)) {
      throw new PropertyNotExistingException(get_class($this), strtolower($name), "setProperty");
    }
    $this->subClassData[strtolower($name)] = $value;
    return $this;
  }
//</editor-fold desc="Private Methods">
}