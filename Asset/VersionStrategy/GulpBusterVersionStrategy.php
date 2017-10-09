<?php

namespace Miisieq\VersionStrategyBundle\Asset\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class GulpBusterVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $hashes;

    /**
     * GulpBusterVersionStrategy constructor.
     *
     * @param $manifestPath
     * @param null $format
     */
    public function __construct($manifestPath, $format = null)
    {
        $this->manifestPath = $manifestPath;
        $this->format = $format ?: '%s?%s';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        if (!is_array($this->hashes)) {
            $this->hashes = $this->loadManifest();
        }

        if (isset($this->hashes["web/$path"])) {
            return $this->hashes["web/$path"];
        }

        return isset($this->hashes[$path]) ? $this->hashes[$path] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        $versioned = sprintf($this->format, ltrim($path, '/'), $version);

        if ($path && '/' === $path[0]) {
            return '/'.$versioned;
        }

        return $versioned;
    }

    /**
     * Load JSON manifest file.
     *
     * @return array
     */
    private function loadManifest()
    {
        return json_decode(file_get_contents($this->manifestPath), true);
    }
}
