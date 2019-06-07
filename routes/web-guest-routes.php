<?php

use App\Http\Controllers\ChangeColorController;
use App\Http\Controllers\CleanSrtController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ConvertToPlainTextController;
use App\Http\Controllers\ConvertToSrtController;
use App\Http\Controllers\ConvertToUtf8Controller;
use App\Http\Controllers\ConvertToVttController;
use App\Http\Controllers\FileGroupArchiveController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MergeController;
use App\Http\Controllers\PinyinController;
use App\Http\Controllers\Redirects;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RequestPasswordResetController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftPartialController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubIdxController;
use App\Http\Controllers\SupController;
use App\Http\Controllers\VerifyAccount;

Route::get('login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('request-password-reset', [RequestPasswordResetController::class, 'index'])->name('requestPasswordReset.index')->middleware('guest');
Route::post('request-password-reset', [RequestPasswordResetController::class, 'post'])->name('requestPasswordReset.post')->middleware('guest');
Route::get('password-reset-requested', [RequestPasswordResetController::class, 'success'])->name('requestPasswordReset.success');

Route::get('reset-password/{token}', [ResetPasswordController::class, 'index'])->name('resetPassword.index');
Route::post('reset-password/{token}', [ResetPasswordController::class, 'post'])->name('resetPassword.post');

Route::get('create-an-account', [RegisterController::class, 'index'])->name('register.index')->middleware('guest');
Route::post('create-an-account', [RegisterController::class, 'post'])->name('register.post')->middleware('guest');
Route::get('create-an-account/verify-email', [RegisterController::class, 'success'])->name('register.success');

Route::get('verify-new-account/{email}/{token}', VerifyAccount::class)->name('verifyEmail');

Route::view('/', 'home')->name('home');

Route::view('/how-to-fix-vlc-subtitles-displaying-as-boxes', 'blogs.fix-vlc-subtitle-boxes')->name('blog.vlcSubtitleBoxes');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'post'])->name('contact.post');

Route::get('/stats', [StatsController::class, 'index'])->name('stats');

Route::post('/file-group-archive/{urlKey}', [FileGroupArchiveController::class, 'download'])->name('fileGroup.archive.download');
Route::get('/file-group-archive/{urlKey}', [FileGroupArchiveController::class, 'downloadRedirect'])->name('fileGroup.archive.downloadRedirect');


Route::get('convert-sub-idx-to-srt-online', [SubIdxController::class, 'index'])->name('subIdx');
Route::post('convert-sub-idx-to-srt-online', [SubIdxController::class, 'post'])->name('subIdx.post');
Route::get('convert-sub-idx-to-srt-online/{urlKey}', [SubIdxController::class, 'show'])->name('subIdx.show');
Route::post('convert-sub-idx-to-srt-online/{urlKey}/{index}', [SubIdxController::class, 'downloadSrt'])->name('subIdx.download');
Route::get('convert-sub-idx-to-srt-online/{urlKey}/{index}', [SubIdxController::class, 'downloadRedirect'])->name('subIdx.downloadRedirect');
Route::post('convert-sub-idx-to-srt-online/{urlKey}/zip/dl', [SubIdxController::class, 'downloadZip'])->name('subIdx.downloadZip');


Route::get('/convert-sup-to-srt-online', [SupController::class, 'index'])->name('sup');
Route::post('/convert-sup-to-srt-online', [SupController::class, 'post'])->name('sup.post');
Route::get('/convert-sup-to-srt-online/{urlKey}', [SupController::class, 'show'])->name('sup.show');
Route::post('/convert-sup-to-srt-online/{urlKey}/download', [SupController::class, 'download'])->name('sup.show.download');
Route::get('/convert-sup-to-srt-online/{urlKey}/download', [SupController::class, 'downloadRedirect'])->name('sup.show.downloadRedirect');


Route::get('/merge-subtitles-online/', [MergeController::class, 'index'])->name('merge');
Route::post('/merge-subtitles-online/', [MergeController::class, 'post'])->name('merge.post');
Route::get('/merge-subtitles-online/{urlKey}', [MergeController::class, 'result'])->name('merge.result');
Route::post('/merge-subtitles-online/{urlKey}/{id}', [MergeController::class, 'download'])->name('merge.download');
Route::get('/merge-subtitles-online/{urlKey}/{id}', [MergeController::class, 'downloadRedirect'])->name('merge.downloadRedirect');


Route::get('/convert-subtitles-to-plain-text-online/', [ConvertToPlainTextController::class, 'index'])->name('convertToPlainText');
Route::post('/convert-subtitles-to-plain-text-online/', [ConvertToPlainTextController::class, 'post'])->name('convertToPlainText.post');
Route::get('/convert-subtitles-to-plain-text-online/{urlKey}', [ConvertToPlainTextController::class, 'result'])->name('convertToPlainText.result');
Route::post('/convert-subtitles-to-plain-text-online/{urlKey}/{id}', [ConvertToPlainTextController::class, 'download'])->name('convertToPlainText.download');
Route::get('/convert-subtitles-to-plain-text-online/{urlKey}/{id}', [ConvertToPlainTextController::class, 'downloadRedirect'])->name('convertToPlainText.downloadRedirect');


Route::get('/convert-text-files-to-utf8-online/', [ConvertToUtf8Controller::class, 'index'])->name('convertToUtf8');
Route::post('/convert-text-files-to-utf8-online/', [ConvertToUtf8Controller::class, 'post'])->name('convertToUtf8.post');
Route::get('/convert-text-files-to-utf8-online/{urlKey}', [ConvertToUtf8Controller::class, 'result'])->name('convertToUtf8.result');
Route::post('/convert-text-files-to-utf8-online/{urlKey}/{id}', [ConvertToUtf8Controller::class, 'download'])->name('convertToUtf8.download');
Route::get('/convert-text-files-to-utf8-online/{urlKey}/{id}', [ConvertToUtf8Controller::class, 'downloadRedirect'])->name('convertToUtf8.downloadRedirect');


Route::get('/partial-subtitle-sync-shifter/', [ShiftPartialController::class, 'index'])->name('shiftPartial');
Route::post('/partial-subtitle-sync-shifter/', [ShiftPartialController::class, 'post'])->name('shiftPartial.post');
Route::get('/partial-subtitle-sync-shifter/{urlKey}', [ShiftPartialController::class, 'result'])->name('shiftPartial.result');
Route::post('/partial-subtitle-sync-shifter/{urlKey}/{id}', [ShiftPartialController::class, 'download'])->name('shiftPartial.download');
Route::get('/partial-subtitle-sync-shifter/{urlKey}/{id}', [ShiftPartialController::class, 'downloadRedirect'])->name('shiftPartial.downloadRedirect');


Route::get('/subtitle-sync-shifter/', [ShiftController::class, 'index'])->name('shift');
Route::post('/subtitle-sync-shifter/', [ShiftController::class, 'post'])->name('shift.post');
Route::get('/subtitle-sync-shifter/{urlKey}', [ShiftController::class, 'result'])->name('shift.result');
Route::post('/subtitle-sync-shifter/{urlKey}/{id}', [ShiftController::class, 'download'])->name('shift.download');
Route::get('/subtitle-sync-shifter/{urlKey}/{id}', [ShiftController::class, 'downloadRedirect'])->name('shift.downloadRedirect');


Route::get('/srt-cleaner/', [CleanSrtController::class, 'index'])->name('cleanSrt');
Route::post('/srt-cleaner/', [CleanSrtController::class, 'post'])->name('cleanSrt.post');
Route::get('/srt-cleaner/{urlKey}', [CleanSrtController::class, 'result'])->name('cleanSrt.result');
Route::post('/srt-cleaner/{urlKey}/{id}', [CleanSrtController::class, 'download'])->name('cleanSrt.download');
Route::get('/srt-cleaner/{urlKey}/{id}', [CleanSrtController::class, 'downloadRedirect'])->name('cleanSrt.downloadRedirect');


Route::get('/convert-to-vtt-online/', [ConvertToVttController::class, 'index'])->name('convertToVtt');
Route::post('/convert-to-vtt-online/', [ConvertToVttController::class, 'post'])->name('convertToVtt.post');
Route::get('/convert-to-vtt-online/{urlKey}', [ConvertToVttController::class, 'result'])->name('convertToVtt.result');
Route::post('/convert-to-vtt-online/{urlKey}/{id}', [ConvertToVttController::class, 'download'])->name('convertToVtt.download');
Route::get('/convert-to-vtt-online/{urlKey}/{id}', [ConvertToVttController::class, 'downloadRedirect'])->name('convertToVtt.downloadRedirect');


Route::get('/convert-to-srt-online/', [ConvertToSrtController::class, 'index'])->name('convertToSrt');
Route::post('/convert-to-srt-online/', [ConvertToSrtController::class, 'post'])->name('convertToSrt.post');
Route::get('/convert-to-srt-online/{urlKey}', [ConvertToSrtController::class, 'result'])->name('convertToSrt.result');
Route::post('/convert-to-srt-online/{urlKey}/{id}', [ConvertToSrtController::class, 'download'])->name('convertToSrt.download');
Route::get('/convert-to-srt-online/{urlKey}/{id}', [ConvertToSrtController::class, 'downloadRedirect'])->name('convertToSrt.downloadRedirect');


Route::get('/make-chinese-pinyin-subtitles/', [PinyinController::class, 'index'])->name('pinyin');
Route::post('/make-chinese-pinyin-subtitles/', [PinyinController::class, 'post'])->name('pinyin.post');
Route::get('/make-chinese-pinyin-subtitles/{urlKey}', [PinyinController::class, 'result'])->name('pinyin.result');
Route::post('/make-chinese-pinyin-subtitles/{urlKey}/{id}', [PinyinController::class, 'download'])->name('pinyin.download');
Route::get('/make-chinese-pinyin-subtitles/{urlKey}/{id}', [PinyinController::class, 'downloadRedirect'])->name('pinyin.downloadRedirect');


Route::get('/change-subtitle-color-online/', [ChangeColorController::class, 'index'])->name('changeColor');
Route::post('/change-subtitle-color-online/', [ChangeColorController::class, 'post'])->name('changeColor.post');
Route::get('/change-subtitle-color-online/{urlKey}', [ChangeColorController::class, 'result'])->name('changeColor.result');
Route::post('/change-subtitle-color-online/{urlKey}/{id}', [ChangeColorController::class, 'download'])->name('changeColor.download');
Route::get('/change-subtitle-color-online/{urlKey}/{id}', [ChangeColorController::class, 'downloadRedirect'])->name('changeColor.downloadRedirect');

Route::get('/format-converter', Redirects::class);
Route::get('/convert-to-srt', Redirects::class);
Route::get('/fo...', Redirects::class);
Route::get('/convert-to-srt-on...', Redirects::class);
Route::get('/c...', Redirects::class);
Route::get('/tools', Redirects::class);
Route::get('/chinese-to-pinyin', Redirects::class);
Route::get('/subtitle-shift', Redirects::class);
Route::get('/partial-subtitle-shifter', Redirects::class);
Route::get('/multi-subtitle-shift', Redirects::class);
Route::get('/convert-to-utf8', Redirects::class);
Route::get('/convert-sub-idx-to-srt', Redirects::class);
