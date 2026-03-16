<?php
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
   error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Root
Route::get('/', 'HomeController@index', function(){
   return View::make("welcome");
});
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/changePassword','HomeController@showChangePasswordForm')->name('password.change');
Route::post('/changePassword','HomeController@changePassword')->name('password.update');

//Auth
Route::auth();
Route::get('/logout', function () {
   Auth::logout();
   return redirect()->back();
});
Auth::routes();

//Applicant
Route::get('/applicant', 'ApplicantController@all');
Route::get('/applicant/all', 'ApplicantController@all');
Route::get('/applicant/add', 'ApplicantController@add');
Route::post('/applicant/add', 'ApplicantController@create');
Route::get('/applicant/{applicant}', 'ApplicantController@show');
Route::post('/applicant/{applicant}', 'ApplicantController@edit');
Route::delete('/applicant/{applicant}', 'ApplicantController@delete');
Route::get('applicant/setPriority/{aID}', 'ApplicantController@setPriority');

//Program
Route::get('/program', 'ProgramController@all');
Route::get('/program/all', 'ProgramController@all');
Route::delete('/program/{program}', 'ProgramController@delete');
// by provider
Route::get('/program/add/{proid}', 'ProgramController@addByProvider');
Route::post('/program/add/{proid}', 'ProgramController@createByProvider');
//
Route::get('/program/{pID}', 'ProgramController@show');
Route::post('/program/{pID}', 'ProgramController@edit');

//Preference
Route::get('/preference/single/{preference}', 'PreferenceController@show');

// ------ By Applicant
Route::get('/preference/applicant/{aID}', 'PreferenceController@showByApplicant');
Route::post('/preference/applicant/{aID}', 'PreferenceController@addByApplicant');
Route::post('/preference/applicant/reorder/{aID}', 'PreferenceController@reorderByApplicantAjax');
Route::post('/preference/applicant/delete/{aID}', 'PreferenceController@deleteByApplicantAjax');
Route::delete('/preference/applicant/{prID}', 'PreferenceController@deleteByApplication');

Route::get('preference/set', 'PreferenceController@setPreferences');
// ------ By Program - coordinated
Route::get('/preference/program/{pID}', 'PreferenceController@showByProgram');
Route::post('/preference/program/{pID}', 'PreferenceController@addByProgram');
Route::delete('/preference/program/{prID}', 'PreferenceController@deleteByProgram');
Route::post('/preference/program/delete/multiple', 'PreferenceController@deleteMultipleByProgram');
Route::post('/preference/program/undo/{pID}', 'PreferenceController@undoByProgram');
Route::post('/preference/program/reorder/{pID}', 'PreferenceController@reorderByProgramAjax');
Route::get('/preferences/program/rebuild/{pID}', 'PreferenceController@rebuildCoordinatedProgramPreferences');

// ------ By Program - uncoordinated
Route::post('/preference/program/uncoordinated/offer/{pID}', 'PreferenceController@addOfferUncoordinatedProgram');
Route::post('/preference/program/uncoordinated/waitlist/{pID}', 'PreferenceController@addWaitlistUncoordinatedProgram');
Route::post('/preference/program/uncoordinated/reorder/{pID}', 'PreferenceController@reorderWaitlistByProgramAjax');
Route::post('/preference/program/uncoordinated/upoffer', 'PreferenceController@updateOfferUncoordinatedProgram');
Route::delete('/preference/program/uncoordinated/{prID}', 'PreferenceController@deleteByProgram');
// ------ All
Route::get('/preference/all', 'PreferenceController@all');

//Guardian
Route::get('/guardian/all', 'GuardianController@all');
Route::get('/guardian/{gID}', 'GuardianController@show');
Route::post('/guardian/{gID}', 'GuardianController@edit');
Route::post('/guardian/verify/{gID}', 'GuardianController@verify');

//Provider
Route::get('/provider/{proid}', 'ProviderController@show');
Route::post('/provider/{proid}', 'ProviderController@edit');

//Matchings
Route::get('/matching/all', 'MatchingController@all');
Route::get('/matching/json', 'MatchingController@createJson');

Route::get('/matching/get', 'MatchingController@findMatchings');

//Criteria
Route::get('/criteria/{p_id}', 'CriteriumController@showByProvider');
Route::post('/criteria', 'CriteriumController@editAjax');
//for uncoordinated progams with no provider, create fake provider
Route::get('/criteria/program/{programId}', 'CriteriumController@showByProgram');
Route::get('/criteria/program/manual/{p_id}', 'CriteriumController@addManualRanking');
Route::post('/criteria/program/reorder/{p_id}', 'CriteriumController@reorderManualRanking');

//Admin
Route::get('/admin/', 'AdminController@index');
Route::get('/admin/dashboard', 'AdminController@index');
Route::get('/admin/export', 'AdminController@exportMatching');
Route::get('/admin/reset', 'AdminController@resetDB');
Route::get('/admin/exportAssigned', 'AdminController@exportAssignedApplicants');
Route::get('/admin/exportUnassigned', 'AdminController@exportUnassignedApplicants');