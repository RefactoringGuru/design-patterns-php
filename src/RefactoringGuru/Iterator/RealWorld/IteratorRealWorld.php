<?php

namespace RefactoringGuru\Iterator\RealWorld;

/**
 * Iterator Design Pattern
 *
 * Intent: Provide a way to access the elements of an aggregate objects without
 * exposing its underlying representation.
 *
 * Example: The Iterator pattern allows easy access to CSV files.
 */

/**
 * CSV File Iterator.
 *
 * @author Josh Lockhart
 */
class CsvIterator implements \Iterator
{
    const ROW_SIZE = 4096;

    /**
     * The pointer to the cvs file.
     *
     * @var resource
     */
    protected $filePointer = null;

    /**
     * The current element, which will be returned on each iteration.
     *
     * @var array
     */
    protected $currentElement = null;

    /**
     * The row counter.
     *
     * @var int
     */
    protected $rowCounter = null;

    /**
     * The delimiter for the csv file.
     *
     * @var string
     */
    protected $delimiter = null;

    /**
     * The constructor tries to open the csv file. It throws an exception on a
     * failure.
     *
     * @param string $file The csv file.
     * @param string $delimiter The delimiter.
     *
     * @throws \Exception
     */
    public function __construct($file, $delimiter = ',')
    {
        try {
            $this->filePointer = fopen($file, 'rb');
            $this->delimiter = $delimiter;
        } catch (\Exception $e) {
            throw new \Exception('The file "'.$file.'" cannot be read.');
        }
    }

    /**
     * This method resets the file pointer.
     */
    public function rewind()
    {
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * This method returns the current csv row as a 2 dimensional array
     *
     * @return array The current csv row as a 2 dimensional array
     */
    public function current()
    {
        $this->currentElement = fgetcsv($this->filePointer, self::ROW_SIZE, $this->delimiter);
        $this->rowCounter++;

        return $this->currentElement;
    }

    /**
     * This method returns the current row number.
     *
     * @return int The current row number
     */
    public function key()
    {
        return $this->rowCounter;
    }

    /**
     * This method checks if the end of file is reached.
     *
     * @return boolean Returns true on EOF reached, false otherwise.
     */
    public function next()
    {
        if (is_resource($this->filePointer)) {
            return ! feof($this->filePointer);
        }

        return false;
    }

    /**
     * This method checks if the next row is a valid row.
     *
     * @return boolean If the next row is a valid row.
     */
    public function valid()
    {
        if (! $this->next()) {
            if (is_resource($this->filePointer)) {
                fclose($this->filePointer);
            }

            return false;
        }

        return true;
    }
}

/**
 * Client code.
 */
$csv = new CsvIterator('cats.csv');

foreach ($csv as $key => $row) {
    print_r($row);
}