<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 AM
 */

namespace App\Http\Controllers;


use App\Entity\Helpers\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
abstract class BaseController extends Controller
{
//<editor-fold desc="Fields">
  /**
   * @var EntityManagerInterface
   */
  protected $em;

  /**
   * @var string
   */
  protected $datetimetzFormat = 'Y-m-d H:i:s e';
//</editor-fold desc="Fields">
//</editor-fold desc="Fields">


//<editor-fold desc="Constructor">
  /**
   * BaseController constructor.
   * @param EntityManagerInterface $em
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Protected Methods">
  /**
   * Gets a transformation function which transforms an enum name into the corresponding value
   * @param string $enum_name the name of the enum
   * @return \Closure the function which transforms a name into the enum value
   */
  protected function enumTransformer(string $enum_name): \Closure
  {
    return function ($x) use ($enum_name) {
      return call_user_func([$enum_name, "getValue"], $x);
    };
  }

  /**
   * Gets a transformation function which transforms a string in datetime format into a datetime with the given timezone
   * @return \Closure the function which transforms a string into a datetime
   */
  protected function datetimetzTransformer(): \Closure
  {
    return function ($x) {
      return \DateTime::createFromFormat($this->datetimetzFormat, $x);
    };
  }

  /**
   * Fills an object with the information of inputArray
   * @param BaseEntity $object the object to fill
   * @param array $specification the specification how to fill the object
   * @param array $inputArray the input array
   * @return mixed the object
   */
  protected function setFromSpecification(BaseEntity $object, array $specification, array $inputArray)
  {
    foreach ($specification as $key => $values) {
      if (!array_key_exists('ignore', $values) || $values['ignore'] != true) {
        $matches = [];
        preg_match('/[^\.]*$/', $key, $matches);
        $arr_key = $matches[0];
        if (array_key_exists('property', $values)) {
          $property = $values['property'];
        } else {
          $property = $arr_key;
        }
        $setter = 'set' . ucfirst($property);
        if (array_key_exists($arr_key, $inputArray)) {
          $value = $inputArray[$arr_key];
          if (array_key_exists('reference', $values)) {
            $value = $this->em->find($values['reference'], $value);
          }
          if (array_key_exists('type', $values)) {
            $value = self::transformByType($value, $values['type']);
          }
          if (array_key_exists('transformer', $values)) {
            $value = $values['transformer']($value);
          }
          $object->$setter($value);
        } else if (array_key_exists('default', $values) && $object->methodExists($setter)) {
          $object->$setter($values['default']);
        }
      }
    }
    return $object;
  }

  /**
   * Validates the parameters of a request by the validate fields of the given specification
   * @param Request $request the request
   * @param array $specification the specification
   * @return $this|BaseController
   */
  protected function validateBySpecification(Request $request, array $specification): BaseController
  {
    $arr = [];
    foreach ($specification as $key => $values) {
      if (array_key_exists('validation', $values)) {
        $arr[$key] = $values['validation'];
      }
    }
    $this->validate($request, $arr);
    return $this;
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Transforms a value from a standard json communication format to its original php format. Counter part of
   * valueToJson().
   * @param string $value the json representation of the value
   * @param string $type the type of the value
   * @return mixed the real php representation of the value
   */
  private static function transformByType($value, $type)
  {
    if (strtolower($type) === 'date' || strtolower($type) === 'datetime') {
      return new \DateTime($value);
    }
    return $value;
  }
//</editor-fold desc="Private Methods">
}