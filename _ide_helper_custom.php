<?php

namespace Illuminate\Http
{
    class Request
    {
        // Macro registered in "Illuminate\Foundation\Providers\FoundationServiceProvider"
        public function validate(array $rules, ...$params)
        {
            return [];
        }
    }
}

namespace Illuminate\Support
{
    class Str
    {
        // Prevent "Method __toString() is not implemented" warnings.
        public function uuid()
        {
            return '';
        }
    }

}
