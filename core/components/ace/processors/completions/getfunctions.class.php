<?php
/**
 * @package ace
 */
class aceCompletionsGetFunctions extends modProcessor {
    public function checkPermissions() {
        return true;
    }
    public function getLanguageTopics() {
        return array();
    }

    public function initialize() {
        return true;
    }

    public function process() {
        $output = array();
        $functions = get_defined_functions();
        foreach ($functions['internal'] as $function) {
            $output[$function] = 'Internal function';
        }
        foreach ($functions['user'] as $function) {
            $output[$function] = 'User function';
        }
        return json_encode($output);
    }
}
return 'aceCompletionsGetFunctions';