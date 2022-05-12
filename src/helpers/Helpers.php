<?php
   
    class Helpers { 

        public static function createOption($value, $text, $selectedValue = NULL){
            if ($value == $selectedValue) 
                return '<option value="'.$value.'" selected>'.$text.'</option>'; 
            else
                return '<option value="'.$value.'">'.$text.'</option>'; 
        }

        /**
         * createOptions()
         * 
         * Creates <option></option> for select elements.
         * 
         * @param array $array must be array of items with index.
         * @param mixed $selectedValue
         * 
         * @return string
         */
        public static function createOptions($array, $selectedValue = null){
            $options = "";
            if (isset($selectedValue) && !empty($selectedValue)) {
                foreach ($array as $item) {
                    $value = $item->value;
                    $text = $item->text;
                    if ($value == $selectedValue) {
                        $options .= '<option value="'.$value.'" selected>'.$text.'</option>';
                    }
                    else {
                        $options .= '<option value="'.$value.'">'.$text.'</option>';
                    }
                } 
            }else {
                foreach ($array as $item) {
                    
                    $options .= '<option value="'.$item->value.'">'.$item->text.'</option>';
                } 
            }
            return $options;
        }

        public static function setValue($value){
            
            if ($value) return $value;
            else return "";
        }
      } 
?>