<?php

namespace Yeanstalk;

/**
 * A Yii application component that provides access to configured instances of
 * {@link http://github.com/pda/pheanstalk/ Pheanstalk}.
 *
 * Add this as a Yii component, and set the `connections` to point to Beanstalkd instances that
 * you want to access using Pheanstalk. Example, in your Yii config file:
 *
 * <code>
 * ...
 * 'components' => array(
 *   'yeanstalk' => array(
 *     'class' => '\\Yeanstalk\\Yeanstalk',
 *     'connections' => array(
 *       'default' => array(
 *         'host' => '127.0.0.1',
 *         'port' => 11300,
 *       ),
 *     ),
 *   ),
 * )
 * ...
 * </code>
 *
 * Then, access your client by:
 *
 * <code>
 * // Get a client configured as "default".
 * $client = \Yii::app()->yeanstalk->getClient('default');
 * </code>
 *
 * The above will return an instance of {@link Pheanstalk} configured using the parameters
 * inside the `"default"` configuration.
 *
 * @author Shiki <shikishiji@gmail.com>
 */
class Yeanstalk extends \CApplicationComponent
{
 /**
  * Configuration for beanstalkd connections. This is an array containing configurations for
  * Beanstalkd instances that you want to connect to. The array items can have these properties:
  *
  * <ul>
  *   <li>`host`</li>
  *   <li>`port`</li>
  *   <li>`connectTimeout`</li>
  * </ul>
  *
  * Sample value:
  *
  * <code>
  * array(
  *   'default' => array(
  *     'host' => '127.0.0.1',
  *     'port' => 11300,
  *   ),
  *   'secondary' => array(
  *     'host' => '127.0.0.1',
  *     'port' => 11301,
  *   ),
  * ),
  * </code>
  *
  * @var array
  */
  public $connections;

  protected $_clients = array();

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();

    if (!is_array($this->connections))
      $this->connections = array();

    if (!class_exists('Pheanstalk', false))
      $this->registerAutoloader();
  }

  /**
   * @param string $connectionName
   * @return \Pheanstalk
   */
  public function getClient($connectionName = 'default')
  {
    if (!isset($this->_clients[$connectionName])) {
      if (!array_key_exists($connectionName, $this->connections))
        throw new \CException('Invalid connection name.');

      $connection = $this->connections[$connectionName];
      if (!isset($connection['port']))
        $connection['port'] = \Pheanstalk::DEFAULT_PORT;
      $client = new \Pheanstalk($connection['host'], $connection['port'],
        isset($connection['connectTimeout']) ? $connection['connectTimeout'] : null);

      $this->_clients[$connectionName] = $client;
    }

    return $this->_clients[$connectionName];
  }

  protected function registerAutoloader()
  {
    $classesPath = dirname(__FILE__) . '/../vendors/Pheanstalk/classes';
    require_once($classesPath . '/Pheanstalk/ClassLoader.php');
    \Pheanstalk_ClassLoader::register($classesPath);

    // Unregister and register with Yii's autoloader so Yii's autoloader will be the last.
    $autoloader = array('Pheanstalk_ClassLoader', 'load');
    spl_autoload_unregister($autoloader);
    \Yii::registerAutoloader($autoloader);
  }
}

