<?php

// ERROR

// global
return Response::json([
  'error' => [
    'message' => 'The deliver with id ' . $id . ' not found',
  ],
], 404);


// fields
return Response::json([
  'error' => [
    'message' => 'bla bla bla',
    'fields' => [
      'username' => 'Must not be empty',
      'password' => 'Must contain at least 8 characters',
    ]
  ],
], 404);


// SUCCESS

return Response::json([
  'response' => [],
], 200);
