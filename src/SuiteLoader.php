<?php
namespace Peridot\Core;

/**
 * SuiteLoader will recursively load test files given a glob friendly
 * pattern.
 *
 * @package Peridot\Core
 */
class SuiteLoader implements SuiteLoaderInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param string $pattern
     * @param Context
     */
    public function __construct($pattern, Context $context)
    {
        $this->pattern = $pattern;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     *
     * @param $path
     */
    public function load($path)
    {
        $tests = $this->getTests($path);
        foreach ($tests as $test) {
            $this->context->setFile($test);
            include $test;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $path
     * @return array
     * @throws \RuntimeException
     */
    public function getTests($path)
    {
        if (is_file($path)) {
            return [$path];
        }
        if (! file_exists($path)) {
            throw new \RuntimeException("Cannot load path $path");
        }
        $pattern = realpath($path) . DIRECTORY_SEPARATOR . $this->pattern;

        return $this->globRecursive($pattern);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Simple recursive glob
     *
     * @link http://php.net/manual/en/function.glob.php#106595
     * @param $pattern
     * @param  int   $flags
     * @return array
     */
    protected function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->globRecursive($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }

        return $files;
    }
}
