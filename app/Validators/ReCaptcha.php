<?php
namespace App\Validators;
use GuzzleHttp\Client;
// class ReCaptcha
// {
//   //https://m.dotdev.co/google-recaptcha-integration-with-laravel-ad0f30b52d7d
//     public function validate($attribute, $value, $parameters, $validator){
//         $client = new Client();
//         $response = $client->post('https://www.google.com/recaptcha/api/siteverify',
//             ['form_params'=>
//                 [
//                     'secret'=>env('GOOGLE_RECAPTCHA_SECRET'),
//                     'response'=>$value
//                  ]
//             ]
//         );
//         $body = json_decode((string)$response->getBody());
//         return $body->success;
//     }
// }
