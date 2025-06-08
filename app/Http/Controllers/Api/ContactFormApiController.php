<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Log;

class ContactFormApiController extends Controller
{
    public function show(Request $request, $id)
    {
        $form = ContactForm::findOrFail($id);

        // 標記為已讀（如果尚未）
        if (!$form->is_read) {
            $form->markAsRead();
        }
        
        return response()->json([
            'message' => $form->message,
        ]);
    }
}
