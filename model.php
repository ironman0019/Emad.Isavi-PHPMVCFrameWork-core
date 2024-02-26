<?php

namespace emadisavi\phpmvc;



abstract class Model {

    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public function loadData($data)
    {
        foreach($data as $key => $value) {

            if(property_exists($this, $key)) {
                
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;
    public $errors = [];

    public function validate()
    {
        foreach($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach($rules as $rule) {

                $ruleName = $rule;
                if(!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute , self::RULE_REQUIRED);
                }

                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }

                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }

                if($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $stmt = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $stmt->bindValue(":attr", $value);
                    $stmt->execute();
                    $record = $stmt->fetchObject();
                    if($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute , string $rule, $params = [])
    {
        $massage = $this->errorMassage()[$rule] ?? '';
        foreach($params as $key => $value) {
            $massage = str_replace("{{$key}}", $value, $massage);
        }

        $this->errors[$attribute][] = $massage; 
    }

    public function addError(string $attribute, string $massage)
    {
        $this->errors[$attribute][] = $massage; 
    }

    public function errorMassage()
    {
        return [

            self::RULE_REQUIRED => "This field is requierd !",
            self::RULE_EMAIL => "This field must be valid email address !",
            self::RULE_MIN => "Min length of this field must be {min} !",
            self::RULE_MAX => "Max length of this field must be {max} !",
            self::RULE_MATCH => "This field must be same as {match} !",
            self::RULE_UNIQUE => "Record with this {field} already exist !",
            
        ];
    }



    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }

    public function labels(): array
    {
        return [];
    }





}