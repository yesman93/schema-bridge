<?php

namespace Lumio\DTO\IO;

use Lumio\Traits;

class XmlResponse {

    use Traits\IO\HttpStatus;

    /**
     * Data to be rendered as XML
     *
     *
     * @var array
     */
    private array $_data = [];

    /**
     * XML string rendered from data
     *
     *
     * @var string
     */
    private string $_data_xml = '';

    /**
     * Length of the XML string (size in bytes)
     *
     *
     * @var int
     */
    private int $_length = 0;

    /**
     * Constructor for XML response
     *
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data) {

        $this->_data = $data;

        $this->_to_xml();
    }

    /**
     * Get XML-safe key
     *
     *
     * @param string|int $key
     *
     * @return string
     */
    private function _xml_key(string|int $key): string {
        return is_numeric($key) ? "item_$key" : preg_replace('/[^a-z0-9_]/i', '', (string) $key);
    }

    /**
     * Convert array to XML string
     *
     *
     * @return void
     */
    private function _to_xml(): void {

        $xml = new \SimpleXMLElement('<response/>');
        $this->_array_to_xml($this->_data, $xml);

        $this->_data_xml = $xml->asXML() ?: '';
        $this->_length = strlen($this->_data_xml);
    }

    /**
     * Recursive array to XML conversion
     *
     *
     * @param array $data
     * @param \SimpleXMLElement $xml
     *
     * @return void
     */
    private function _array_to_xml(array $data, \SimpleXMLElement $xml): void {

        foreach ($data as $key => $value) {

            $key = $this->_xml_key($key);

            if (is_array($value)) {
                $child = $xml->addChild($key);
                $this->_array_to_xml($value, $child);
            } else {
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }

    /**
     * Get original array data
     *
     *
     * @return array
     */
    public function get_data(): array {
        return $this->_data;
    }

    /**
     * Get rendered XML string
     *
     *
     * @return string
     */
    public function __toString(): string {
        return $this->_data_xml;
    }

    /**
     * Get length of XML data
     *
     *
     * @return int
     */
    public function get_length(): int {
        return $this->_length;
    }

}
