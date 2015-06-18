<?php
/**
 * Created by PhpStorm.
 * User: vagenas
 * Date: 16/10/2014
 * Time: 12:03 μμ
 */

namespace totos;

use xd_v141226_dev\exception;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}

class xml extends \xd_v141226_dev\xml
{
    /**
     * @var array
     */
    protected $ttsXMLFields
      = array(
        'UniqueID',
        'Name',
        'Link',
        'Image',
        'Category',
        'Price_with_vat',
        'Stock',
        'Availability',
        'Manufacturer',
        'MPN',
        'ISBN',
        'Size',
        'Color',
      );

    /**
     * @var array
     */
    protected $ttsXMLFieldsLengths
      = array(
        'UniqueID'       => 200,
        'Name'           => 300,
        'Link'           => 1000,
        'Image'          => 400,
        'Category'       => 250,
        'Price_with_vat' => 0,
        'Stock'          => 0,
        'Availability'   => 60,
        'Manufacturer'   => 100,
        'MPN'            => 80,
        'ISBN'           => 80,
        'Size'           => 500,
        'Color'          => 100,
      );

    /**
     * @var array
     */
    protected $ttsXMLRequiredFields
      = array(
        'UniqueID',
        'Name',
        'Link',
        'Image',
        'Category',
        'Price_with_vat',
        'Stock',
        'Availability',
        'Manufacturer',
      );

    /**
     * @var \SimpleXMLExtended
     */
    public $simpleXML = null;

    /**
     * Absolute file path
     *
     * @var string
     */
    public $fileLocation = '';

    /**
     * @var null
     */
    public $createdAt = null;
    /**
     * @var string
     */
    public $createdAtName = 'created_at';

    /**
     * @var string
     */
    protected $rootElemName = 'MyStore';

    /**
     * @var string
     */
    protected $productElemName = 'product';

    /**
     * @param array $array
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function parseArray(Array $array)
    {
        // init simple xml if is not initialized already
        if (!$this->simpleXML) {
            $this->initSimpleXML();
        }

        // parse array
        foreach ($array as $k => $v) {
            $this->appendProduct($v);
        }

        return !empty($array) && $this->saveXML();
    }

    /**
     * @param array $p
     *
     * @return int
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150130
     */
    public function appendProduct(Array $p)
    {
        if (!$this->simpleXML) {
            $this->initSimpleXML();
        }

        $validated = $this->validateArrayKeys($p);

        if (!empty($validated)) {
            $product = $this->simpleXML->addChild($this->productElemName);

            foreach ($validated as $key => $value) {
                if ($this->isValidXmlName($value)) {
                    $product->addChild($key, $value);
                } else {
                    $product->$key = null;
                    $product->$key->addCData($value);
                }
            }

            return 1;
        }

        return 0;
    }

    /**
     * @return $this
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function initSimpleXML()
    {
        $this->fileLocation = $this->getFileLocation();

        $this->simpleXML = new \SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><'.$this->rootElemName.'></'.$this->rootElemName.'>');

        return $this;
    }

    /**
     * @param array $array
     *
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function validateArrayKeys(Array $array)
    {
        foreach ($this->ttsXMLRequiredFields as $fieldName) {
            if (!isset($array[$fieldName]) || empty($array[$fieldName])) {
                $fields = array();
                foreach ($this->ttsXMLRequiredFields as $f) {
                    if (!isset($array[$f]) || empty($array[$f])) {
                        array_push($fields, $f);
                    }
                }
                $name = isset($array['Name']) ? $array['Name'] : (isset($array['UniqueID']) ? 'with id '.$array['UniqueID'] : '');
                $this->©diagnostic->forceDBLog(
                  'product',
                  $array,
                  'Product <strong>'.$name.'</strong> not included in XML file because field(s) '.implode(', ', $fields).' is/are missing or is invalid'
                );

                return array();
            } else {
                $array[$fieldName] = $this->trimField($array[$fieldName], $fieldName);
                if (is_string($array[$fieldName])) {
                    $array[$fieldName] = mb_convert_encoding($array[$fieldName], "UTF-8");
                }
            }
        }

        foreach ($array as $k => $v) {
            if (!in_array($k, $this->ttsXMLFields)) {
                unset($array[$k]);
            }
        }

        return $array;
    }

    protected function isValidXmlName($name)
    {
        try {
            new \DOMElement($name);

            return true;
        } catch (\DOMException $e) {
            return false;
        }
    }

    /**
     * @param $value
     * @param $fieldName
     *
     * @return bool|string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function trimField($value, $fieldName)
    {
        if (!isset($this->ttsXMLFieldsLengths[$fieldName])) {
            return false;
        }

        if ($this->ttsXMLFieldsLengths[$fieldName] === 0) {
            return $value;
        }

        return substr((string)$value, 0, $this->ttsXMLFieldsLengths[$fieldName]);
    }

    /**
     * @return bool
     * @throws exception
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function loadXML()
    {
        /**
         * For now we write it from scratch EVERY TIME
         */
        $this->fileLocation = $this->getFileLocation();

        return false;
    }

    /**
     * @param       $prodId
     * @param array $newValues
     *
     * @return bool|mixed
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function updateProductInXML($prodId, Array $newValues)
    {
        $newValues = $this->validateArrayKeys($newValues);
        if (empty($newValues)) {
            return false;
        }
        // init simple xml if is not initialized already
        if (!$this->simpleXML) {
            $this->initSimpleXML();
        }

        $p = $this->locateProductNode($prodId);
        if (!$p) {
            $p = $this->simpleXML->addChild($this->productElemName);
        }
        foreach ($newValues as $key => $value) {
            if ($this->isValidXmlName($value)) {
                $p->addChild($key, $value);
            } else {
                $p->$key = null;
                $p->$key->addCData($value);
            }
        }

        return $this->saveXML();
    }

    /**
     * @param $nodeId
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function locateProductNode($nodeId)
    {
        if (!($this->simpleXML instanceof \SimpleXMLElement)) {
            return false;
        }

        foreach ($this->simpleXML->product as $k => $p) {
            if ($p->id == $nodeId) {
                return $p;
            }
        }

        return false;
    }

    /**
     * @return bool|mixed
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function saveXML()
    {
        if (!($this->simpleXML instanceof \SimpleXMLExtended)) {
            return false;
        }
        $dir = dirname($this->fileLocation);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($this->simpleXML && !empty($this->fileLocation) && (is_writable($this->fileLocation) || is_writable($dir))) {
            if (is_file($this->fileLocation)) {
                unlink($this->fileLocation);
            }
            $this->simpleXML->addChild($this->createdAtName, date('Y-m-d H:i'));

            return $this->simpleXML->asXML($this->fileLocation);
        }

        return false;
    }

    /**
     * Print SimpleXMLElement $this->simpleXML to screen
     *
     * @throws exception
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function printXML()
    {
        if (headers_sent()) {
            return;
        }

        if (!($this->simpleXML instanceof \SimpleXMLExtended)) {
            $fileLocation = $this->getFileLocation();
            if (!$this->existsAndReadable($fileLocation)) {
                return;
            }
            $this->simpleXML = simplexml_load_file($fileLocation);
        }

        header("Content-Type:text/xml");

        echo $this->simpleXML->asXML();

        exit(0);
    }

    /**
     * Returns the file location based on settings (even if it isn't exists)
     *
     * @return string
     * @throws exception
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function getFileLocation()
    {
        $location = $this->©options->get('xml_location');
        $fileName = $this->©options->get('xml_fileName');

        $location = empty($location) || $location == '/' ? '' : (trim($location, '\\/').'/');

        return rtrim(ABSPATH, '\\/').'/'.$location.trim($fileName, '\\/');
    }

    /**
     * Get XML file info
     *
     * @return array|null
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function getFileInfo()
    {
        $fileLocation = $this->getFileLocation();

        if ($this->existsAndReadable($fileLocation)) {
            $info = array();

            $sXML         = simplexml_load_file($fileLocation);
            $cratedAtName = $this->createdAtName;

            $info[$this->createdAtName] = array(
              'value' => end($sXML->$cratedAtName),
              'label' => 'Cached File Creation Datetime'
            );

            $info['productCount'] = array(
              'value' => $this->countProductsInFile($sXML),
              'label' => 'Number of Products Included'
            );

            $info['cachedFilePath'] = array('value' => $fileLocation, 'label' => 'Cached File Path');

            $info['url'] = array(
              'value' => $this->©url->to_wp_site_uri(str_replace(ABSPATH, '', $fileLocation)),
              'label' => 'Cached File Url'
            );

            $info['size'] = array('value' => filesize($fileLocation), 'label' => 'Cached File Size');

            return $info;
        } else {
            return null;
        }
    }

    /**
     * Counts total products in file
     *
     * @param $file string|\SimpleXMLExtended|\SimpleXMLElement
     *
     * @return int Total products in file
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    public function countProductsInFile($file)
    {
        if ($this->existsAndReadable($file)) {
            $sXML = simplexml_load_file($file);
        } elseif ($file instanceof \SimpleXMLElement || $file instanceof \SimpleXMLExtended) {
            $sXML = &$file;
        } else {
            return 0;
        }

        if ($sXML->getName() == $this->rootElemName) {
            return $sXML->count();
        }

        return 0;
    }

    /**
     * Checks if file exists and is readable
     *
     * @param $file string File location
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  150610
     */
    protected function existsAndReadable($file)
    {
        return is_string($file) && file_exists($file) && is_readable($file);
    }
}
