<?php

/// Validator crashed with PHP7.4, PHP8 :/

///namespace App\Includes;
///
///use Respect\Validation\Validator as V;
///
///class ValidationRules {
///
///
///	/// POST /users
///    function usersPost() {
///        return [
///            'name' => self::notBlank()->alpha()['name'],
///            'email' => self::notBlank()->email()['email']
///        ];
///    }
///
///
///    /// POST /phones
///    function phonesPost() {
///        return [
///            'phonenumber' => self::common()->noWhitespace()['phonenumber']
///        ];
///    }
///
///
///} /// END Of ValidationRules Class