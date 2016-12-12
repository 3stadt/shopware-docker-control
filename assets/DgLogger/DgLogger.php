<?php

namespace DgLogger;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\Common\Util\ClassUtils;
use stdclass;

class DgLogger
{
    private $file;

    public function __construct($file = null)
    {
        if ($file == null) {
            $this->file = '/var/www/html/DgLoggerFile.txt';
        } else {
            $this->file = $file;
        }
    }

    public function write($toLog, $maxDepth = 2, $stripTags = true, $echo = false)
    {
        $string = $this->dump($toLog, $maxDepth, $stripTags, $echo);
        $string.= PHP_EOL;

        file_put_contents($this->file, $string, FILE_APPEND);
    }

    /**
     * Prints a dump of the public, protected and private properties of $var.
     *
     * @link http://xdebug.org/
     *
     * @param mixed $var The variable to dump.
     * @param integer $maxDepth The maximum nesting level for object properties.
     * @param boolean $stripTags Whether output should strip HTML tags.
     * @param boolean $echo Send the dumped value to the output buffer
     *
     * @return string
     */
    private function dump($var, $maxDepth, $stripTags, $echo)
    {
        $html = ini_get('html_errors');

        if ($html !== true) {
            ini_set('html_errors', true);
        }

        if (extension_loaded('xdebug')) {
            ini_set('xdebug.var_display_max_depth', $maxDepth);
        }

        $var = $this->export($var, $maxDepth++);

        ob_start();
        var_dump($var);

        $dump = ob_get_contents();

        ob_end_clean();

        $dumpText = ($stripTags ? strip_tags(html_entity_decode($dump)) : $dump);

        ini_set('html_errors', $html);

        if ($echo) {
            echo $dumpText;
        }

        return $dumpText;
    }

    /**
     * @param mixed $var
     * @param int $maxDepth
     *
     * @return mixed
     */
    private function export($var, $maxDepth)
    {
        $return = null;
        $isObj = is_object($var);

        if ($var instanceof Collection) {
            $var = $var->toArray();
        }

        if ($maxDepth) {
            if (is_array($var)) {
                $return = [];

                foreach ($var as $k => $v) {
                    $return[$k] = $this->export($v, $maxDepth - 1);
                }
            } elseif ($isObj) {
                $return = new stdclass();
                if ($var instanceof DateTime) {
                    $return->__CLASS__ = "DateTime";
                    $return->date = $var->format('c');
                    $return->timezone = $var->getTimeZone()->getName();
                } else {
                    $reflClass = ClassUtils::newReflectionObject($var);
                    $return->__CLASS__ = ClassUtils::getClass($var);

                    if ($var instanceof Proxy) {
                        $return->__IS_PROXY__ = true;
                        $return->__PROXY_INITIALIZED__ = $var->__isInitialized();
                    }

                    if ($var instanceof \ArrayObject || $var instanceof \ArrayIterator) {
                        $return->__STORAGE__ = $this->export($var->getArrayCopy(), $maxDepth - 1);
                    }

                    foreach ($reflClass->getProperties() as $reflProperty) {
                        $name = $reflProperty->getName();

                        $reflProperty->setAccessible(true);
                        $return->$name = $this->export($reflProperty->getValue($var), $maxDepth - 1);
                    }
                }
            } else {
                $return = $var;
            }
        } else {
            $return = is_object($var) ? get_class($var)
                : (is_array($var) ? 'Array(' . count($var) . ')' : $var);
        }

        return $return;
    }

    /**
     * Returns a string representation of an object.
     *
     * @param object $obj
     *
     * @return string
     */
    public function toString($obj)
    {
        return method_exists($obj, '__toString') ? (string)$obj : get_class($obj) . '@' . spl_object_hash($obj);
    }
}
