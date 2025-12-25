<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentCollectionController;

// API Routes

Route::post('/webhooks/document-received', [DocumentCollectionController::class, 'processIncomingDocuments']);