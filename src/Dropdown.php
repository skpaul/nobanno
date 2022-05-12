<?php
   
    class DropDown { 

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
                    $value = $item[0];
                    $text = $item[1];
                    if ($value == $selectedValue) {
                        $options .= '<option value="'.$value.'" selected>'.$text.'</option>';
                    }
                    else {
                        $options .= '<option value="'.$value.'">'.$text.'</option>';
                    }
                } 
            }else {
                foreach ($array as $item) {
                    $value = $item[0];
                    $text = $item[1];
                    $options .= '<option value="'.$value.'">'.$text.'</option>';
                } 
            }
            return $options;
        }

        public static function setValue($value){
            
            if ($value) return $value;
            else return "";
        }

        public static function isItemSelected($currentOptionValue, $valueToCompare){
            $selected = "";
            if ($currentOptionValue == $valueToCompare) {
                $selected =  "selected";
            }
        
            return $selected;
        }


      } 
?>