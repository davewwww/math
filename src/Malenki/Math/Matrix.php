<?php
/*
 * Copyright (c) 2014 Michel Petit <petit.michel@gmail.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


namespace Malenki\Math;


/**
 * Matrix basis implementation.
 * 
 * @todo as a reminder: http://www.latp.univ-mrs.fr/~torresan/CalcMat/cours/node2.html
 *
 * @property-read $cols Amount of columns
 * @property-read $rows Amount of rows
 * @author Michel Petit <petit.michel@gmail.com> 
 * @license MIT
 */
class Matrix
{
    protected $arr = array();
    protected $size = null;



    public function __get($name)
    {
        if(in_array($name, array('cols', 'rows')))
        {
            return $this->size->$name;
        }
    }



    public function __construct($int_cols, $int_rows)
    {
        if(!is_numeric($int_cols) || !is_numeric($int_rows))
        {
            throw new \InvalidArgumentException('number of cols and rows must be integers.');
        }

        $int_cols = (integer) $int_cols;
        $int_rows = (integer) $int_rows;

        if($int_cols <= 0 || $int_rows <= 0)
        {
            throw new \InvalidArgumentException('Number of cols and rows must be positive not null integers.');
        }

        $this->size = new \stdClass();
        $this->size->cols = $int_cols;
        $this->size->rows = $int_rows;
    }



    public function addRow(array $arr_row)
    {
        if(count($this->arr) == $this->size->rows)
        {
            throw new \OutOfRangeException(sprintf('You cannot add another row! Max number of rows is %d', $this->size->rows));
        }

        if(count($arr_row) != $this->size->cols)
        {
            throw new \InvalidArgumentException('New row must have same amout of columns than previous rows.');
        }

        $this->arr[] = $arr_row;

        return $this;
    }



    public function addCol($arr_col)
    {
        if(isset($this->arr[0]) && (count($this->arr[0]) == $this->size->cols))
        {
            throw new \OutOfRangeException(sprintf('You cannot add another column! Max number of columns is %d', $this->size->cols));
        }

        if(count($arr_col) != $this->size->rows)
        {
            throw new \InvalidArgumentException('New column must have same amout of rows than previous columns.');
        }

        $arr_col = array_values($arr_col); //to be sure to have index 0, 1, 2…

        foreach($arr_col as $k => $v)
        {
            $this->arr[$k][] = $arr_col[$k];
        }


        return $this;
    }



    public function getRow($int)
    {
        if(!isset($this->arr[$int]))
        {
            throw new \OutOfRangeException('There is no line having this index.');
        }

        return $this->arr[$int];
    }



    public function getCol($int)
    {
        if($int >= $this->size->cols)
        {
            throw new \OutOfRangeException('There is not column having this index.');
        }

        $arr_out = array();

        foreach($this->arr as $row)
        {
            $arr_out[] = $row[$int];
        }

        return $arr_out;
    }



    public function isSquare()
    {
        return $this->size->cols == $this->size->rows;
    }



    public function isVector()
    {
        return $this->size->cols == 1;
    }


    public function multiplyAllow($matrix)
    {
        if(is_numeric($matrix))
        {
            return true;
        }
        
        if($matrix instanceof \Malenki\Math\Complex)
        {
            return true;
        }

        if($matrix instanceof \Malenki\Math\Matrix)
        {
            return $this->size->cols == $matrix->rows;
        }

        return false;
    }



    public function add()
    {
    }


    public function multiply($mix)
    {
        //TODO use complex numbers too
        if(!$this->multiplyAllow($mix))
        {
            throw new \InvalidArgumentException('Invalid number or matrix has not right number of rows.');
        }


        if($mix instanceof \Malenki\Math\Matrix)
        {
            $out = new self($mix->cols, $this->size->rows);

            for($r = 0; $r < $this->size->rows; $r++)
            {
                $arrOutRow = array();

                for($c = 0; $c < $mix->cols; $c++)
                {
                    $arrCol = $mix->getCol($c);
                    $arrRow = $this->getRow($r);

                    $arrItem = array();

                    foreach($arrCol as $k => $v)
                    {
                        $arrItem[] = $arrRow[$k] * $v;
                    }

                    $arrOutRow[] = array_sum($arrItem);
                }

                $out->addRow($arrOutRow);
            }

            return $out;
        }

        if(is_numeric($mix))
        {
            $out = new self($this->size->cols, $this->size->rows);

            for($r = 0; $r < $this->size->rows; $r++)
            {
                $arrRow = $this->getRow($r);

                foreach($arrRow as $k => $v)
                {
                    $arrRow[$k] = $mix * $v;
                }

                $out->addRow($arrRow);
            }

            return $out;
        }
    }



    public function getAll()
    {
        return $this->arr;
    }
}
