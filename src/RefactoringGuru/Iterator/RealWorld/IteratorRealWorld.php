<?php

namespace RefactoringGuru\Iterator\RealWorld;

/**
 * EN: Iterator Design Pattern
 *
 * Intent: Provide a way to access the elements of an aggregate object without
 * exposing its underlying representation.
 *
 * Example: Since PHP already has a built-in Iterator interface, which provides
 * convenient integration with foreach loops, it is very easy to create your own
 * iterators for traversing almost every imaginable data structure.
 *
 * This example of the Iterator pattern provides easy access to CSV files.
 *
 * RU: Паттерн Итератор
 *
 * Назначение: Предоставляет способ доступа к элементам составного объекта, не
 * раскрывая его внутреннего представления.
 *
 * Пример: Так как PHP уже имеет встроенный интерфейс Итератора, который
 * предоставляет удобную интеграцию с циклами foreach, очень легко создать
 * собственные итераторы для обхода практически любой мыслимой структуры данных.
 *
 * Этот пример паттерна Итератор предоставляет лёгкий доступ к CSV-файлам.
 */

/**
 * EN: CSV File Iterator.
 *
 * @author Josh Lockhart
 *
 * RU: Итератор CSV-файлов.
 *
 * @author Josh Lockhart
 */
class CsvIterator implements \Iterator
{
    const ROW_SIZE = 4096;

    /**
     * EN: The pointer to the CSV file.
     *
     * @var resource
     *
     * RU: Указатель на CSV-файл.
     *
     * @var resource
     */
    protected $filePointer = null;

    /**
     * EN: The current element, which is returned on each iteration.
     *
     * @var array
     *
     * RU: Текущий элемент, который возвращается на каждой итерации.
     *
     * @var array
     */
    protected $currentElement = null;

    /**
     * EN: The row counter.
     *
     * @var int
     *
     * RU: Счётчик строк.
     *
     * @var int
     */
    protected $rowCounter = null;

    /**
     * EN: The delimiter for the CSV file.
     *
     * @var string
     *
     * RU: Разделитель для CSV-файла.
     *
     * @var string
     */
    protected $delimiter = null;

    /**
     * EN: The constructor tries to open the CSV file. It throws an exception on
     * failure.
     *
     * @param string $file The CSV file.
     * @param string $delimiter The delimiter.
     *
     * @throws \Exception
     *
     * RU: Конструктор пытается открыть CSV-файл. Он выдаёт исключение при
     * ошибке.
     *
     * @param string $file CSV-файл.
     * @param string $delimiter Разделитель.
     *
     * @throws \Exception
     */
    public function __construct($file, $delimiter = ',')
    {
        try {
            $this->filePointer = fopen($file, 'rb');
            $this->delimiter = $delimiter;
        } catch (\Exception $e) {
            throw new \Exception('The file "' . $file . '" cannot be read.');
        }
    }

    /**
     * EN: This method resets the file pointer.
     *
     * RU: Этот метод сбрасывает указатель файла.
     */
    public function rewind(): void
    {
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * EN: This method returns the current CSV row as a 2-dimensional array.
     *
     * @return array The current CSV row as a 2-dimensional array.
     *
     * RU: Этот метод возвращает текущую CSV-строку в виде двумерного массива.
     *
     * @return array Текущая CSV-строка в виде двумерного массива.
     */
    public function current(): array
    {
        $this->currentElement = fgetcsv($this->filePointer, self::ROW_SIZE, $this->delimiter);
        $this->rowCounter++;

        return $this->currentElement;
    }

    /**
     * EN: This method returns the current row number.
     *
     * @return int The current row number.
     *
     * RU: Этот метод возвращает номер текущей строки.
     *
     * @return int Номер текущей строки.
     */
    public function key(): int
    {
        return $this->rowCounter;
    }

    /**
     * EN: This method checks if the end of file has been reached.
     *
     * @return bool Returns true on EOF reached, false otherwise.
     *
     * RU: Этот метод проверяет, достигнут ли конец файла.
     *
     * @return bool Возвращает true при достижении EOF, в ином случае false.
     */
    public function next(): bool
    {
        if (is_resource($this->filePointer)) {
            return !feof($this->filePointer);
        }

        return false;
    }

    /**
     * EN: This method checks if the next row is a valid row.
     *
     * @return bool If the next row is a valid row.
     *
     * RU: Этот метод проверяет, является ли следующая строка допустимой.
     *
     * @return bool Если следующая строка является допустимой.
     */
    public function valid(): bool
    {
        if (!$this->next()) {
            if (is_resource($this->filePointer)) {
                fclose($this->filePointer);
            }

            return false;
        }

        return true;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
$csv = new CsvIterator(__DIR__ . '/cats.csv');

foreach ($csv as $key => $row) {
    print_r($row);
}
