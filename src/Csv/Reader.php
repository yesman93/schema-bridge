<?php

namespace SchemaBridge\Csv;

use RuntimeException;
use SplFileObject;

class Reader
{

    /**
     * File object
     *
     * @var SplFileObject
     */
    private SplFileObject $_file;

    /**
     * Delimiter character
     *
     * @var string
     */
    private string $_delimiter;

    /**
     * CSV file reader
     *
     * @param string $path      Path to CSV file
     * @param string $delimiter Delimiter character, default is comma
     *
     * @return void
     *
     * @throws RuntimeException If file cannot be opened
     */
    public function __construct(string $path, string $delimiter = ',')
    {

        $this->_file = new SplFileObject($path, 'r');
        $this->_file->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        $this->_delimiter = $delimiter;
    }

    /**
     * Return the first N rows from the CSV file
     *
     * @param int $rows Number of rows to return, default is 50
     *
     * @return array
     */
    public function preview(int $rows = 50): array
    {

        $this->_file->setCsvControl($this->_delimiter);

        $out = [];
        $i = 0;
        foreach ($this->_file as $row) {

            if ($row === [null] || $row === false) {
                continue;
            }

            $out[] = array_map(
                static fn($v) => is_string($v) ? trim($v) : $v,
                $row
            );

            if (++$i >= $rows) {
                break;
            }
        }

        return $out;
    }

}
