<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Fallback Only)
|--------------------------------------------------------------------------
| This is a minimal fallback. Main system uses API + React.
*/

// Route::prefix('web')->group(function () {

//     Route::get('/', function () {
//         return 'Web fallback active';
//     });
    
// });

Route::get('/web', function () {
    return 'Web fallback active';
});

Route::get('/', function () {
    return redirect('/admin/login');
});