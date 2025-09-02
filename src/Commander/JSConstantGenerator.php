<?php

namespace Lumio\Commander;

use Lumio\File\File;
use ReflectionClass;
use Throwable;

class JSConstantGenerator {

    /**
     * Generate JavaScript constants from PHP class constants
     *
     * @author TB
     * @date 24.7.2025
     *
     * @param string $output_path
     * @param bool $log_constants Whether to log constants in the browser console
     *
     * @return bool
     */
    public function generate(string $output_path, bool $log_constants = false): bool {

        $output = $this->_get_constants();

        $js_code = "/**" . PHP_EOL;
        $js_code .= " * JS constants" . PHP_EOL;
        $js_code .= " *" . PHP_EOL;
        $js_code .= " * @date " . date('j.n.Y') . " " . PHP_EOL;
        $js_code .= " * @time " . date('G:i:s') . " " . PHP_EOL;
        $js_code .= " *" . PHP_EOL;
        $js_code .= " */" . PHP_EOL;
        $js_code .= PHP_EOL;
        $js_code .= PHP_EOL;

        $js_code .= $this->_generate_js_object('window', $output);

        if ($log_constants) {

            $globals = [];
            if (isset($output['GlobalConstants'])) foreach ($output['GlobalConstants'] as $name => $value) {
                $globals[] = $name . ': window.' . $name;
            }
            $globals = implode(', ', $globals);

            $lumios = 'Lumio: window.Lumio';

            $js_code .= PHP_EOL . 'console.log(\'constants from PHP: \', {' . $globals . ', ' . $lumios . '});' . PHP_EOL;
        }

        if ($this->_write_file($output_path, $js_code)) {
            return true;
        }

        return false;
    }

    /**
     * Parse a literal value from given PHP expression
     *
     * @author TB
     * @date 24.7.2025
     *
     * @param string $expression
     *
     * @return mixed
     */
    private function _parse_literal(string $expression): mixed {

        $expression = trim($expression);

        // Match single or double quoted string
        if (preg_match('/^[\'"](.*)[\'"]$/', $expression, $m)) {
            return stripcslashes($m[1]);
        }

        // Match integer or float
        if (is_numeric($expression)) {
            return $expression + 0;
        }

        // Match booleans
        $lower = strtolower($expression);
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }

        // Match null
        if ($lower === 'null') {
            return null;
        }

        // Unknown format — skip
        return null;
    }

    /**
     * Assign constants to a nested array structure based on the path segments
     *
     * @author TB
     * @date 24.7.2025
     *
     * @param array $target
     * @param array $path_segments
     * @param array $constants
     *
     * @return void
     */
    private function _assign_nested_array(array &$target, array $path_segments, array $constants): void {
        $ref =& $target;

        foreach ($path_segments as $segment) {
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref =& $ref[$segment];
        }

        $ref = $constants;
    }

    /**
     * Retrieve all constants (general, classes and traits) from PHP files in the project
     *
     * @author TB
     * @date 24.7.2025
     *
     * @return array
     */
    private function _get_constants(): array {

        $output = [];

        $files_app = File::map_directory(ROOT_PATH . 'app');
        $files_config = File::map_directory(ROOT_PATH . 'config');
        $files_src = File::map_directory(ROOT_PATH . 'src');
        $files_www = [ROOT_PATH . 'www/index.php'];

        $files = array_merge($files_app, $files_config, $files_src, $files_www);

        foreach ($files as $file) {

            if (!is_file($file) || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $has_const = false;
            $is_class = false;
            $is_trait = false;
            $namespace = '';
            $class_name = '';

            $handle = fopen($file, 'r');
            if (!$handle) {
                continue;
            }

            while (($line = fgets($handle)) !== false) {

                if (preg_match('/^\s*(public\s+)?const\s([a-z]+\s+)?+[A-Z_]+\s*=/', $line)) {
                    $has_const = true;
                }

                if (preg_match('/^\s*trait\s+(\w+)/', $line, $m)) {
                    $class_name = $m[1];
                    $is_trait = true;
                }

                if (preg_match('/^\s*class\s+(\w+)/', $line, $m)) {
                    $class_name = $m[1];
                    $is_class = true;
                }

                if (preg_match('/^namespace\s+(.+);/', $line, $m)) {
                    $namespace = trim($m[1]);
                }
            }

            fclose($handle);

//            // Rewind the file again to extract top-level constants
//            if ($has_const && !$is_class && !$is_trait) {
//
//                $handle = fopen($file, 'r');
//                if ($handle) {
//
//                    while (($line = fgets($handle)) !== false) {
//
//                        // Match constants defined like: const FOO = 'bar'; or define('FOO', 'bar');
//                        if (preg_match('/^\s*const\s+([A-Z_][A-Z0-9_]*)\s*=\s*(.+);/', $line, $m)) {
//                            $name = $m[1];
//                            $value = $this->_parse_literal($m[2]);
//                            $output['GlobalConstants'][$name] = $value;
//                        } else if (preg_match('/^\s*define\s*\(\s*[\'"]([A-Z_][A-Z0-9_]*)[\'"]\s*,\s*(.+)\);\s*/i', $line, $m)) {
//                            $name = $m[1];
//                            $value = $this->_parse_literal($m[2]);
//                            $output['GlobalConstants'][$name] = $value;
//                        }
//                    }
//
//                    fclose($handle);
//                }
//            }



            if ($has_const && ($is_class || $is_trait)) {


                $fqcn = "$namespace\\$class_name";
                if (!class_exists($fqcn) && !trait_exists($fqcn)) {
                    continue;
                }

                try {

                    $ref = new ReflectionClass($fqcn);
                    $constants = $ref->getConstants();
                    if (empty($constants)) {
                        continue;
                    }

                    $path_segments = explode('\\', $fqcn);
                    $this->_assign_nested_array($output, $path_segments, $constants);

                } catch (Throwable $e) {
                    continue;
                }
            }

        }

        $global_constants = get_defined_constants(true);
        $global_constants = $global_constants['user'] ?? [];
        if (!empty($global_constants)) {

            // Some constants like encryption salts, paths, etc. are either security risk or not useful in JS context
            $ignore = [
                'ENCRYPTION_SALT',
                'LUMIO_DEV',
                'LUMIO_ENV',
                'LUMIO_HOST',
                'LUMIO_PROD',
                'LUMIO_TEST',
                'ROOT_PATH',
                'SSL_ENC_KEY',
            ];
            foreach ($ignore as $name) {
                unset($global_constants[$name]);
            }

            $output = ['GlobalConstants' => $global_constants] + $output;
        }

        return $output;
    }

    /**
     * Generate JavaScript object code from the given data
     *
     * @author TB
     * @date 24.7.2025
     *
     * @param string $root
     * @param array $data
     * @param int $level
     *
     * @return string
     */
    private function _generate_js_object(string $root, array $data, int $level = 0): string {

        $js = '';
        foreach ($data as $key => $value) {

            $path = $root;
            if ($key != 'GlobalConstants') {
                $path .= '.' . $key;
            }

            if (is_array($value)) {

                $js .= PHP_EOL . "{$path} = {$path} || {};\n";
                $js .= $this->_generate_js_object($path, $value, $level + 1);

            } else {

                $js_value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $js .= $root . '[\'' . $key . '\'] = ' . $js_value . ';' . PHP_EOL;
            }
        }

        return $js;
    }

    /**
     * Write the generated JavaScript code to the specified output file
     *
     * @author TB
     * @date 24.7.2025
     *
     * @param string $output_path
     * @param string $js_code
     *
     * @return bool
     */
    private function _write_file(string $output_path, string $js_code): bool {

        touch($output_path);

        return file_put_contents($output_path, $js_code) ? true : false;
    }

}
