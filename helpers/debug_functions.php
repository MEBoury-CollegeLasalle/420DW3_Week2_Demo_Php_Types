<?php

/*
 * exercises1 debug_functions.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-01-05
 * (c) Copyright 2023 Marc-Eric Boury 
 */

declare(strict_types=1);

/**
 * Basic debug helper function. Generates an HTML table string for whatever value is provided in <code>$input</code>.
 * The table will contain the type data of the <code>$input</code> value, and its value(s). For container-types values
 * (arrays, objects...), the function is recursive and will display each element or property of the container-type.
 *
 * By default, the string is echoed before the function returns it.
 *
 * @param mixed $input  The value to debug
 * @param bool  $doEcho OPTIONAL: Whether to echo the generated HTML table string before returning it or not.
 *                      Defaults to <code>true</code>
 * @param bool  $doDie  OPTIONAL: Wheter to stop execution after echoing the HTML table. Defaults to <code>false</code>
 *
 * @return string
 *
 * @author Marc-Eric Boury
 * @since  2023-01-05
 */
function debug(mixed $input, bool $doEcho = true, bool $doDie = false) : string {
    $return_value = "<table style='border: 1px solid black; border-collapse: collapse;'>";
    $input_type = gettype($input);
    switch ($input_type) {
        case "boolean":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>".($input ? "true" : "false")."</td></tr>";
            break;
        case "integer":
        case "double":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            break;
        case "string":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>\"$input\"</td></tr>";
            break;
        case "NULL":
            $return_value .= "<tr><td style='border: 1px solid black;'>null</td></tr>";
            break;
        case "array":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'><table style='border: 1px solid black; border-collapse: collapse;'>";
            foreach ($input as $key => $value) {
                $key_name = $key;
                if (!is_numeric($key)) {
                    $key_name = "\"$key\"";
                }
                $return_value .= "<tr><td style='border: 1px solid black;'>$key_name</td><td style='border: 1px solid black;'>".
                              debug($value, false)."</td>";
            }
            $return_value .= "</table></td></tr>";
            break;
        case "object":
            try {
                $reflection_class = new ReflectionClass($input);
                $return_value .= "<tr><td style='border: 1px solid black;'>".$reflection_class->getShortName().
                              "</td><td style='border: 1px solid black;'><table style='border: 1px solid black; border-collapse: collapse;'>";
                $properties = $reflection_class->getProperties();
                foreach ($properties as $property) {
                    $return_value .= "<tr><td style='border: 1px solid black;'>\"".$property->getName().
                                  "\"</td><td style='border: 1px solid black;'>".
                                  debug($property->getValue($input), false)."</td>";
                }
                $return_value .= "</table></td></tr>";
            } catch (ReflectionException $refl_ex) {
                $return_value .= "<tr><td style='border: 1px solid black;'>ReflectionException thrown: ".
                              $refl_ex->getMessage()."</td></tr>";
            }
            break;
        case "resource":
        case "resource (closed)":
        case "unknown type":
        default:
            try {
                $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            } catch (Exception $exception) {
                $return_value .= "<tr><td style='border: 1px solid black;'>unstringifyable $input_type</td></tr>";
            }
            break;
    }
    $return_value .= "</table>";
    if ($doEcho) {
        echo $return_value;
    }
    if ($doDie) {
        die(0);
    }
    return $return_value;
}

/**
 * A simple object class used for testing. Contains properties of various types.
 *
 * @author Marc-Eric Boury
 * @since  2023-01-05
 */
class TestObject {
    public readonly int $anInt;
    public readonly float $aFloat;
    public readonly string $aString;
    public readonly array $aNumericArray;
    public readonly array $anAssociativeArray;
    
    public function __construct() {
        $this->anInt = 5;
        $this->aFloat = 2.0594578;
        $this->aString = "I'm a string!";
        $this->aNumericArray = [0 => 1, 1 => "stringy", 2 => ["a", "b", "c"]];
        $this->anAssociativeArray = [
            "firstEntry" => 1, "secondEntry" => "stringy", "thirdEntry" => ["a", "b", "c"]];
    }
}